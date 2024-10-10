<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
  

    public function stock()
{
    return $this->belongsTo(Stock::class);
}

public function transactions()
{
    return $this->hasMany(Transaction::class);
}

}
