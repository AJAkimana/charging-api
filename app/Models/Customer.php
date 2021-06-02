<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, Notifiable, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'msisdn',
        'location',
        'customer_status',
        'kyc_id',
        'offer_id',
        'picture',
        'application_form',
        'next_of_kin_id',
        'loan_status',
        'monthversary_date',
        'installment_period',
        'installment_amount'
    ];

    public function kyc(): BelongsTo
    {
        return $this->belongsTo(Kyc::class);
    }
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
    public function nextOfKin(): BelongsTo
    {
        return $this->belongsTo(NextOfKeen::class);
    }
    public function installmentsPaid(): HasMany
    {
        return $this->hasMany('customers', ['customer_fk']);
    }
}
