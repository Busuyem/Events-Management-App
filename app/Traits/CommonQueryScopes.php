<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait CommonQueryScopes
{
    public function scopeFilterByDate(Builder $query, ?string $date): Builder
    {
        if ($date) {
            $query->whereDate('date', Carbon::parse($date)->toDateString());
        }
        return $query;
    }

    public function scopeSearchByTitle(Builder $query, ?string $title): Builder
    {
        if ($title && trim($title) !== '') {
            $query->where('title', 'like', '%' . trim($title) . '%');
        }

        return $query;
    }
}
