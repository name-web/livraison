<?php

namespace App\Models\Backend;

use App\Enums\CollectionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Collection extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'merchant_id',
        'delivery_man_id',
        'shop_id',
        'status',
        'pickup_address',
        'pickup_lat',
        'pickup_long',
        'collection_date',
        'time_slot',
        'scheduled_at',
        'parcel_count',
        'total_cash_collection',
        'total_delivery_amount',
        'assigned_at',
        'picked_up_at',
        'collected_at',
        'note',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'collected_at' => 'datetime',
        'total_cash_collection' => 'decimal:2',
        'total_delivery_amount' => 'decimal:2',
        'pickup_lat' => 'decimal:7',
        'pickup_long' => 'decimal:7',
    ];

    // ─── Activity Log ──────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Collection')
            ->logOnly(['status', 'delivery_man_id', 'parcel_count', 'total_cash_collection'])
            ->setDescriptionForEvent(fn (string $eventName) => "{$eventName}");
    }

    // ─── Relations ─────────────────────────────────

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Backend\Merchant::class);
    }

    public function deliveryMan(): BelongsTo
    {
        return $this->belongsTo(DeliveryMan::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(\App\Models\MerchantShops::class, 'shop_id');
    }

    public function parcels(): BelongsToMany
    {
        return $this->belongsToMany(Parcel::class, 'collection_parcels')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function cashTrackings(): HasMany
    {
        return $this->hasMany(CashTracking::class);
    }

    // ─── Accessors ─────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            CollectionStatus::PENDING_ASSIGNMENT => 'En attente',
            CollectionStatus::ASSIGNED => 'Affectée',
            CollectionStatus::PICKING_UP => 'En cours de ramassage',
            CollectionStatus::COLLECTED => 'Collectée',
            CollectionStatus::COMPLETED => 'Terminée',
            CollectionStatus::CANCELLED => 'Annulée',
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            CollectionStatus::PENDING_ASSIGNMENT => 'warning',
            CollectionStatus::ASSIGNED => 'info',
            CollectionStatus::PICKING_UP => 'primary',
            CollectionStatus::COLLECTED => 'success',
            CollectionStatus::COMPLETED => 'success',
            CollectionStatus::CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    // ─── Scopes ────────────────────────────────────

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('collection_date', $date);
    }

    public function scopePending($query)
    {
        return $query->where('collections.status', CollectionStatus::PENDING_ASSIGNMENT);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('collections.status', [
            CollectionStatus::COMPLETED,
            CollectionStatus::CANCELLED,
        ]);
    }
}
