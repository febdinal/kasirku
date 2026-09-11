<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    public const PRESET_CATEGORIES = [
        'Umum',
        'New Customer',
        'Tetap',
        'Loyal',
        'Reseller',
    ];

    protected $fillable = ['name', 'category', 'phone', 'email', 'address'];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
