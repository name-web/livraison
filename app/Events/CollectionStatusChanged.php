<?php

namespace App\Events;

use App\Models\Backend\Collection;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CollectionStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Collection $collection
    ) {}

    public function broadcastAs(): string
    {
        return 'collection.status.changed';
    }

    /**
     * Les channels sur lesquels cet event est diffusé.
     *
     * - Privé pour le marchand concerné
     * - Privé pour le livreur affecté
     * - Canal admin global
     */
    public function broadcastWith(): array
    {
        return [
            'collection' => [
                'id' => $this->collection->id,
                'merchant_id' => $this->collection->merchant_id,
                'delivery_man_id' => $this->collection->delivery_man_id,
                'status' => $this->collection->status,
                'status_label' => $this->collection->status_label,
                'status_color' => $this->collection->status_color,
                'parcel_count' => $this->collection->parcel_count,
                'total_cash_collection' => $this->collection->total_cash_collection,
                'total_delivery_amount' => $this->collection->total_delivery_amount,
                'pickup_address' => $this->collection->pickup_address,
                'pickup_lat' => $this->collection->pickup_lat,
                'pickup_long' => $this->collection->pickup_long,
                'assigned_at' => $this->collection->assigned_at?->toISOString(),
                'picked_up_at' => $this->collection->picked_up_at?->toISOString(),
                'collected_at' => $this->collection->collected_at?->toISOString(),
                'created_at' => $this->collection->created_at->toISOString(),
            ],
            'updated_at' => now()->toISOString(),
        ];
    }

    public function broadcastOn(): array
    {
        $channels = [];

        // Channel privé du marchand
        $channels[] = new PrivateChannel('merchant.collection.'.$this->collection->merchant_id);

        // Channel privé du livreur (si affecté)
        if ($this->collection->delivery_man_id) {
            $channels[] = new PrivateChannel('deliveryman.collection.'.$this->collection->delivery_man_id);
        }

        // Channel admin global
        $channels[] = new PrivateChannel('admin.collections');

        return $channels;
    }
}
