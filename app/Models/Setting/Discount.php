<?php

namespace App\Models\Setting;

use App\Casts\RateCast;
use App\Concerns\Blamable;
use App\Concerns\CompanyOwned;
use App\Concerns\HasDefault;
use App\Concerns\SyncsWithCompanyDefaults;
use App\Enums\Setting\DiscountComputation;
use App\Enums\Setting\DiscountScope;
use App\Enums\Setting\DiscountType;
use App\Models\Accounting\Account;
use Database\Factories\Setting\DiscountFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discount extends Model
{
    use Blamable;
    use CompanyOwned;
    use HasDefault;
    use HasFactory;
    use SyncsWithCompanyDefaults;

    protected $table = 'discounts';

    protected $fillable = [
        'company_id',
        'account_id',
        'rate',
        'computation',
        'type',
        'scope',
        'start_date',
        'end_date',
        'enabled',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rate' => RateCast::class,
        'computation' => DiscountComputation::class,
        'type' => DiscountType::class,
        'scope' => DiscountScope::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'enabled' => 'boolean',
    ];

    protected ?string $evaluatedDefault = 'type';

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function defaultSalesDiscount(): HasOne
    {
        return $this->hasOne(CompanyDefault::class, 'sales_discount_id');
    }

    public function defaultPurchaseDiscount(): HasOne
    {
        return $this->hasOne(CompanyDefault::class, 'purchase_discount_id');
    }

    public function adjustmentables(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory(): Factory
    {
        return DiscountFactory::new();
    }
}
