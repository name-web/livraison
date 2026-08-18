<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Canal personnel utilisateur
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// ─── Collectes ──────────────────────────────────

// Marchand : voit les collectes de ses propres commandes
Broadcast::channel('merchant.collection.{merchantId}', function ($user, $merchantId) {
    return (int) $user->merchant->id === (int) $merchantId;
});

// Livreur : voit les collectes qui lui sont assignées
Broadcast::channel('deliveryman.collection.{deliverymanId}', function ($user, $deliverymanId) {
    return (int) $user->deliveryman->id === (int) $deliverymanId;
});

// Admin : voit toutes les collectes
Broadcast::channel('admin.collections', function ($user) {
    return $user->user_type === 1;
});

// ─── Suivi GPS ──────────────────────────────────

// Admin : voit tous les livreurs sur la carte
Broadcast::channel('admin.map', function ($user) {
    return $user->user_type === 1;
});

// Livreur : sa propre position
Broadcast::channel('deliveryman.location.{deliverymanId}', function ($user, $deliverymanId) {
    return (int) $user->deliveryman->id === (int) $deliverymanId;
});

// Marchand : voit le GPS du livreur affecté à ses collectes
Broadcast::channel('merchant.gps.{merchantId}', function ($user, $merchantId) {
    return (int) $user->merchant->id === (int) $merchantId;
});
