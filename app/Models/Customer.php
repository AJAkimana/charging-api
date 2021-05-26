<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ];

    public function kyc(): BelongsTo
    {
        return $this->belongsTo(Kyc::class);
    }
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
