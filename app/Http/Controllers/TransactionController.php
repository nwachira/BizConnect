<?php

namespace App\Http\Controllers;

use App\Models\Transaction; 
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function transaction(Transaction $record)
    {
        // Retrieve the transaction data
        $customerName = "Unganisha Networks"; // You can fetch this from a related model if available
        $customerEmail = "unganishanetworks@gmail.com.com"; // You can fetch this from a related model if available

        $itemName = $record->service->name; // Assuming service relationship exists
        $pricePerUnit = $record->amount_earned / $record->quantity_used; // Calculate price per unit
        $quantityUsed = $record->quantity_used;
        $amountEarned = $record->amount_earned;

        // Format amountEarned as KSh
        $formattedAmountEarned = 'KSh ' . number_format($amountEarned, 2); 

        // Create the buyer using static or fetched customer data
        $customer = new Buyer([
            'name' => $customerName,
            'custom_fields' => [
                'email' => $customerEmail,
            ],
        ]);

        // Create the invoice item
        $item = InvoiceItem::make($itemName)
            ->pricePerUnit($pricePerUnit)
            ->quantity($quantityUsed);

        // Create the invoice and set currency to KSh
        $invoice = Invoice::make('Receipt') // Set the title here
            ->buyer($customer)
            ->addItem($item)
            ->currencySymbol('KSh') // Set currency to KSh
            ->currencyCode('KES') // Set currency code to Kenyan Shillings
            ->discountByPercent(0) // Adjust if needed
            ->taxRate(0) // Adjust if needed
            ->shipping(0); // Adjust if needed

        // You may want to set invoice properties like title, date, etc.
        $invoice->date($record->created_at)
                ->filename("receipt-{$record->id}.pdf"); // Optional filename for the receipt

        // Stream the receipt to the user
        return $invoice->stream();
    }
}
