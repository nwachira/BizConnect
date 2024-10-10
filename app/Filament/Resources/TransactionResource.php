<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Service; 
use App\Filament\Resources\TransactionResource\Widgets\SalesOverview; 
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Sales';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $get, $state) {
                        $serviceId = $get('service_id');
                        $service = Service::find($serviceId);

                        if ($service) {
                            $set('quantity_used', $service->price_per_unit ? round($get('amount_earned') / $service->price_per_unit, 2) : 0);
                        }
                    }),

                Forms\Components\TextInput::make('amount_earned')
                    ->required()
                    ->numeric()
                    ->prefix('KSh') // Add the KSh prefix to the input field
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $get, $state) {
                        $serviceId = $get('service_id');
                        $service = Service::find($serviceId);

                        if ($service) {
                            $quantityUsed = $service->price_per_unit ? round($state / $service->price_per_unit, 2) : 0;
                            $set('quantity_used', $quantityUsed);

                            // Deduct stock in real-time
                            if ($service->stock && $service->stock->quantity >= $quantityUsed) {
                                $service->stock->decrement('quantity', $quantityUsed);
                            } else {
                                // Handle insufficient stock scenario (optional: display error)
                                $set('amount_earned', null); // Clear amount earned if insufficient stock
                            }
                        }
                    }),

                Forms\Components\TextInput::make('quantity_used')
                    ->readOnly()
                    ->numeric(),

                Forms\Components\Select::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'mpesa' => 'Mpesa',
                    ])
                    ->required() // Make this required for better data integrity
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_earned')
                    ->formatStateUsing(fn ($state) => "KSh {$state}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_used')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Ensure the action is properly structured
                Tables\Actions\Action::make('Print Receipt')
                    ->icon('heroicon-o-printer')
                    ->url(fn(Transaction $record) => route('transaction.pdf.download', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define any relationships here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function create(array $data)
    {
        // Find the service associated with the transaction
        $service = Service::find($data['service_id']);
        
        // Ensure the service exists and calculate quantity used
        if ($service && $service->price_per_unit) {
            // Calculate the quantity used based on the amount earned
            $quantity_used = $data['amount_earned'] / $service->price_per_unit;

            // Check if the service has stock and if there is enough quantity
            if ($service->stock && $service->stock->quantity >= $quantity_used) {
                // Deduct stock if sufficient quantity is available
                $service->stock->decrement('quantity', $quantity_used);
                
                // Add the calculated quantity_used to the transaction data
                $data['quantity_used'] = $quantity_used;
            } else {
                // You can throw an exception or handle insufficient stock scenario here
                throw new \Exception("Not enough stock available for the service.");
            }
        }
    
        // Proceed with creating the transaction
        return parent::create($data);
    }

    public static function getWidgets(): array
    {
        return [
            SalesOverview::class,
        ];
    }
}
