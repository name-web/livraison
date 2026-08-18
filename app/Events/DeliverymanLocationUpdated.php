<?php

namespace App\Events;

use App\Models\Backend\Collection;
use App\Models\Backend\DeliveryMan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliverymanLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public DeliveryMan $deliveryman
    ) {}

    public function broadcastAs(): string
    {
        return 'deliveryman.location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'deliveryman' => [
                'id' => $this->deliveryman->id,
                'user_id' => $this->deliveryman->user_id,
                'name' => $this->deliveryman->user->name,
                'lat' => $this->deliveryman->current_location_lat,
                'lng' => $this->deliveryman->current_location_long,
                'is_available' => $this->deliveryman->is_available,
            ],
            'updated_at' => now()->toISOString(),
        ];
    }

    /**
     * Channels :
     * - admin.map : super admin (tous les livreurs)
     * - deliveryman.location.{id} : le livreur lui-même
     * - merchant.gps.{merchantId} : chaque marchand dont le livreur a une collecte active
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('admin.map'),
            new PrivateChannel('deliveryman.location.'.$this->deliveryman->id),
        ];

        // Ajouter les marchands concernés
        $merchantIds = Collection::where('delivery_man_id', $this->deliveryman->id)
            ->active()
            ->pluck('merchant_id')
            ->unique();

        foreach ($merchantIds as $merchantId) {
            $channels[] = new PrivateChannel('merchant.gps.'.$merchantId);
        }

        return $channels;
    }
}
