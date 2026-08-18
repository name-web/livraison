<?php

use App\Enums\Wallet\WalletStatus;

return [
    WalletStatus::PENDING  => 'En attente',
    WalletStatus::APPROVED => 'Validée',
    WalletStatus::REJECTED => 'Rejetée',
];