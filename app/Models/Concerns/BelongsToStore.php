<?php

namespace App\Models\Concerns;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Mengisolasi data per toko: semua query otomatis difilter ke toko milik
 * user yang sedang login, dan store_id terisi otomatis saat data dibuat.
 */
trait BelongsToStore
{
    protected static function bootBelongsToStore(): void
    {
        static::addGlobalScope('store', function (Builder $builder) {
            if (Auth::hasUser() || Auth::check()) {
                $storeId = Auth::user()->currentStoreId();

                $storeId
                    ? $builder->where($builder->getModel()->getTable().'.store_id', $storeId)
                    : $builder->whereRaw('1 = 0');

                return;
            }

            // Tanpa user login tidak ada data toko yang terlihat.
            // Untuk kebutuhan konsol/seeder gunakan Auth::login() atau withoutGlobalScopes().
            $builder->whereRaw('1 = 0');
        });

        static::creating(function ($model) {
            if (empty($model->store_id) && Auth::check()) {
                $model->store_id = Auth::user()->currentStoreId();
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
