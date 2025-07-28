<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
class Transaction extends Model
{
    protected $fillable = [
        'type',
        'currency_id',
        'amount',
        'description',
        'date',
        'payment_method',
        'reference',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    /**
     * Scope a query to only include transactions of a given type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include transactions within a date range.
     */
    public function scopeBetweenDates(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query->when($from, function (Builder $q) use ($from) {
            $q->where('date', '>=', Carbon::parse($from));
        })
        ->when($to, function (Builder $q) use ($to) {
            $q->where('date', '<=', Carbon::parse($to));
        });
    }

    /**
     * Get the sum of amounts for a specific type in a period.
     */
    public static function getSumByType(string $type, ?string $from = null, ?string $to = null): float
    {
        return self::ofType($type)
            ->betweenDates($from, $to)
            ->sum('amount');
    }

    /**
     * Get all transactions of a specific type in a period.
     */
    public static function getByType(string $type, ?string $from = null, ?string $to = null)
    {
        return self::ofType($type)
            ->betweenDates($from, $to)
            ->with('currency')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Get summary of all transaction types in a period.
     */
    public static function getTypeSummary(?string $from = null, ?string $to = null): array
    {
        $expenses = self::getSumByType('expense', $from, $to);
        $payments = self::getSumByType('payment', $from, $to);

        return [
            'expense' => $expenses,
            'payment' => $payments,
            'total' => $expenses + $payments,
        ];
    }
}
