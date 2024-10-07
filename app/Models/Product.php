<?php

namespace App\Models;

use App\Enums\ProductPeriod;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'period' => ProductPeriod::class,
        ];
    }

    public function isBilledMonthly(): Attribute
    {
        return Attribute::get(fn (): bool => $this->period === ProductPeriod::monthly);
    }

    public function isBilledYearly(): Attribute
    {
        return Attribute::get(fn (): bool => $this->period === ProductPeriod::yearly);
    }
}
