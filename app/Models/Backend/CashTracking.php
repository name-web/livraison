<?php

namespace App\Models\Backend;

use App\Enums\CashTrackingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CashTracking extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'collection_id',
        'parcel_id',
        'delivery_man_id',
        'merchant_id',
        'amount_expected',
        'amount_collected',
        'amount_handed_over',
        'amount_remaining',
        'status',
        'anomaly_note',
        'handed_over_to',
        'collected_at',
        'handed_over_at',
    ];

    protected $casts = [
        'amount_expected' => 'decimal:2',
        'amount_collected' => 'decimal:2',
        'amount_handed_over' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
        'collected_at' => 'datetime',
        'handed_over_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('CashTracking')
            ->logOnly(['amount_expected', 'amount_collected', 'amount_handed_over', 'status'])
            ->setDescriptionForEvent(fn (string $eventName) => "{$eventName}");
    }

    // ─── Relations ─────────────────────────────────

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(Parcel::class);
    }

    public function deliveryMan(): BelongsTo
    {
        return $this->belongsTo(DeliveryMan::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    // ─── Accessors ─────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            CashTrackingStatus::PENDING => 'En attente',
            CashTrackingStatus::COLLECTED => 'Encaissé',
            CashTrackingStatus::HANDED_OVER => 'Remis',
            CashTrackingStatus::RECONCILED => 'Réconcilié',
            CashTrackingStatus::ANOMALY => 'Anomalie',
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            CashTrackingStatus::PENDING => 'warning',
            CashTrackingStatus::COLLECTED => 'info',
            CashTrackingStatus::HANDED_OVER => 'primary',
            CashTrackingStatus::RECONCILED => 'success',
            CashTrackingStatus::ANOMALY => 'danger',
            default => 'secondary',
        };
    }

    public function getDifferenceAttribute(): float
    {
        return $this->amount_expected - $this->amount_collected;
    }
}
