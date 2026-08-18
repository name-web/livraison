<?php

namespace App\Models\Backend;

use App\Enums\SupportStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Support extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'department_id',
        'service',
        'priority',
        'subject',
        'description',
        'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $logAttributes = [
            'user.name',
            'department.title',
            'service',
            'priority',
            'subject',
            'description',
            'date',
        ];

        return LogOptions::defaults()
            ->useLogName('Support')
            ->logOnly($logAttributes)
            ->setDescriptionForEvent(fn (string $eventName) => "{$eventName}");
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function file()
    {
        return $this->belongsTo(Upload::class, 'attached_file', 'id');
    }

    public function attached_file()
    {
        return $this->belongsTo(Upload::class, 'attached_file', 'id');
    }

    public function getAttachedAttribute()
    {
        if (! empty($this->attached_file->original['original']) && file_exists(public_path($this->attached_file->original['original']))) {
            return static_asset($this->attached_file->original['original']);
        }

        return static_asset('images/default/user.png');
    }

    public function supportChats()
    {
        return $this->hasMany(SupportChat::class, 'support_id', 'id');
    }

    /**
     * Retourne le CSS class du badge selon le statut.
     */
    public function getStatusColorAttribute(): string
    {
        return match ((int) $this->status) {
            SupportStatus::PENDING => 'wc-st-chip-pending',
            SupportStatus::PROCESSING => 'wc-st-chip-processing',
            SupportStatus::RESOLVED => 'wc-st-chip-resolved',
            SupportStatus::CLOSED => 'wc-st-chip-closed',
            default => 'wc-st-chip-pending',
        };
    }

    /**
     * Retourne le label du statut.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            SupportStatus::PENDING => __('levels.pending'),
            SupportStatus::PROCESSING => __('levels.processing'),
            SupportStatus::RESOLVED => __('levels.resolved'),
            SupportStatus::CLOSED => __('levels.closed'),
            default => __('levels.pending'),
        };
    }

    /**
     * Retourne le CSS class du badge de priorité.
     */
    public function getPriorityColorAttribute(): string
    {
        return match (strtolower($this->priority ?? '')) {
            'high' => 'wc-st-prio-high',
            'medium' => 'wc-st-prio-medium',
            'low' => 'wc-st-prio-low',
            default => 'wc-st-prio-medium',
        };
    }
}
