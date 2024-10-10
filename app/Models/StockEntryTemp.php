<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntryTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_entry_id',

        'action',
    ];

    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class, 'stock_entry_id');
    }
}
