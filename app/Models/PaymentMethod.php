<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_cash',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_cash' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope a query to only include active payment methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
