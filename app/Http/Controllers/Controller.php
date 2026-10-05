<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

abstract class Controller
{
    /**
     * Rule unique yang dibatasi pada toko user yang sedang login.
     */
    protected function uniqueInStore(string $table, string $column): Unique
    {
        return Rule::unique($table, $column)->where('store_id', auth()->user()->currentStoreId());
    }

    /**
     * Rule exists yang dibatasi pada toko user yang sedang login.
     */
    protected function existsInStore(string $table, string $column = 'id'): Exists
    {
        return Rule::exists($table, $column)->where('store_id', auth()->user()->currentStoreId());
    }
}
