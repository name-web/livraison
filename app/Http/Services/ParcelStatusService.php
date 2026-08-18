<?php

namespace App\Http\Services;

use App\Enums\ParcelStatus;
use App\Enums\TodoStatus;
use App\Models\Backend\Parcel;

class ParcelStatusService
{
    /**
     * Statuts autorisés pour chaque état du colis.
     */
    private const STATUS_TRANSITIONS = [
        ParcelStatus::PENDING => [
            ParcelStatus::PICKUP_ASSIGN,
        ],
        ParcelStatus::PICKUP_ASSIGN => [
            ParcelStatus::PICKUP_ASSIGN_CANCEL,
            ParcelStatus::PICKUP_RE_SCHEDULE,
            ParcelStatus::RECEIVED_BY_PICKUP_MAN,
            ParcelStatus::RECEIVED_WAREHOUSE,
        ],
        ParcelStatus::PICKUP_RE_SCHEDULE => [
            ParcelStatus::PICKUP_RE_SCHEDULE_CANCEL,
            ParcelStatus::PICKUP_RE_SCHEDULE,
            ParcelStatus::RECEIVED_BY_PICKUP_MAN,
            ParcelStatus::RECEIVED_WAREHOUSE,
        ],
        ParcelStatus::RECEIVED_BY_PICKUP_MAN => [
            ParcelStatus::RECEIVED_BY_PICKUP_MAN_CANCEL,
            ParcelStatus::RECEIVED_BY_PICKUP_MAN,
            ParcelStatus::RECEIVED_WAREHOUSE,
        ],
        ParcelStatus::RECEIVED_WAREHOUSE => [
            ParcelStatus::RECEIVED_WAREHOUSE_CANCEL,
            ParcelStatus::TRANSFER_TO_HUB,
            ParcelStatus::DELIVERY_MAN_ASSIGN,
        ],
        ParcelStatus::RECEIVED_BY_HUB => [
            ParcelStatus::RECEIVED_BY_HUB_CANCEL,
            ParcelStatus::TRANSFER_TO_HUB,
            ParcelStatus::DELIVERY_MAN_ASSIGN,
        ],
        ParcelStatus::TRANSFER_TO_HUB => [
            ParcelStatus::TRANSFER_TO_HUB_CANCEL,
            ParcelStatus::RECEIVED_BY_HUB,
        ],
        ParcelStatus::DELIVERY_MAN_ASSIGN => [
            ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
            ParcelStatus::DELIVERY_RE_SCHEDULE,
            ParcelStatus::RETURN_TO_COURIER,
            ParcelStatus::DELIVERED,
            ParcelStatus::PARTIAL_DELIVERED,
        ],
        ParcelStatus::DELIVERY_RE_SCHEDULE => [
            ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
            ParcelStatus::DELIVERY_RE_SCHEDULE,
            ParcelStatus::RETURN_TO_COURIER,
            ParcelStatus::DELIVERED,
            ParcelStatus::PARTIAL_DELIVERED,
        ],
        ParcelStatus::RETURN_TO_COURIER => [
            ParcelStatus::RETURN_TO_COURIER_CANCEL,
            ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
        ],
        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT => [
            ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
            ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
            ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
        ],
        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE => [
            ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE_CANCEL,
            ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
            ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
        ],
    ];

    /**
     * Mapping statut → classe CSS badge.
     */
    private const STATUS_BADGE_MAP = [
        ParcelStatus::PENDING => 'badge-danger',
        ParcelStatus::PICKUP_ASSIGN => 'badge-primary',
        ParcelStatus::PICKUP_RE_SCHEDULE => 'badge-dark',
        ParcelStatus::RECEIVED_BY_PICKUP_MAN => 'badge-success',
        ParcelStatus::RECEIVED_WAREHOUSE => 'badge-info',
        ParcelStatus::DELIVERY_MAN_ASSIGN => 'badge-warning',
        ParcelStatus::DELIVERY_RE_SCHEDULE => 'badge-info',
        ParcelStatus::RETURN_TO_COURIER => 'badge-info',
        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT => 'badge-dark',
        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE => 'badge-dark',
        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT => 'badge-success',
        ParcelStatus::DELIVER => 'badge-success',
        ParcelStatus::DELIVERED => 'badge-success',
        ParcelStatus::PARTIAL_DELIVERED => 'badge-success',
        ParcelStatus::RETURN_WAREHOUSE => 'badge-info',
        ParcelStatus::ASSIGN_MERCHANT => 'badge-secondary',
        ParcelStatus::RETURNED_MERCHANT => 'badge-dark',
        ParcelStatus::TRANSFER_TO_HUB => 'badge-info',
        ParcelStatus::RECEIVED_BY_HUB => 'badge-info',
    ];

    /**
     * Mapping statut → icône Font Awesome.
     */
    private const STATUS_ICON_MAP = [
        ParcelStatus::PENDING => 'fas fa-hourglass-end',
        ParcelStatus::PICKUP_ASSIGN => 'fas fa-truck',
        ParcelStatus::PICKUP_RE_SCHEDULE => 'fas fa-truck',
        ParcelStatus::RECEIVED_WAREHOUSE => 'fas fa-warehouse',
        ParcelStatus::TRANSFER_TO_HUB => 'fa fa-right-left',
        ParcelStatus::RECEIVED_BY_HUB => 'fa fa-warehouse',
        ParcelStatus::DELIVERY_MAN_ASSIGN => 'fa fa-people-carry',
        ParcelStatus::DELIVERY_RE_SCHEDULE => 'fa fa-people-carry',
        ParcelStatus::DELIVERED => 'fas fa-handshake',
        ParcelStatus::PARTIAL_DELIVERED => 'fas fa-handshake',
        ParcelStatus::RETURN_TO_COURIER => 'fa fa-warehouse',
        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT => 'fas fa-truck',
        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE => 'fas fa-truck',
        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT => 'fas fa-store',
    ];

    /**
     * Mapping cancel → route name.
     */
    private const CANCEL_ROUTE_MAP = [
        ParcelStatus::PICKUP_ASSIGN_CANCEL => 'parcel.pickup.man-assigned-cancel',
        ParcelStatus::PICKUP_RE_SCHEDULE_CANCEL => 'parcel.pickup.re-schedule-cancel',
        ParcelStatus::RECEIVED_BY_PICKUP_MAN_CANCEL => 'parcel.pickup.man-received-cancel',
        ParcelStatus::RECEIVED_WAREHOUSE_CANCEL => 'parcel.received-warehouse-cancel',
        ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL => 'parcel.delivery-man-assign-cancel',
        ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL => 'parcel.delivery-re-schedule-cancel',
        ParcelStatus::TRANSFER_TO_HUB_CANCEL => 'parcel.transfer-to-hub-cancel',
        ParcelStatus::RECEIVED_BY_HUB_CANCEL => 'parcel.received-by-hub-cancel',
        ParcelStatus::RETURN_TO_COURIER_CANCEL => 'parcel.return-to-courier-cancel',
        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL => 'parcel.return-assign-to-merchant-cancel',
        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE_CANCEL => 'parcel.return-assign-re-schedule-to-merchant-cancel',
        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT_CANCEL => 'parcel.return-received-by-merchant-cancel',
        ParcelStatus::DELIVERED_CANCEL => 'parcel.delivered-cancel',
        ParcelStatus::PARTIAL_DELIVERED_CANCEL => 'parcel.partial-delivered-cancel',
    ];

    /**
     * Mapping cancel → classe CSS.
     */
    private const CANCEL_CSS_MAP = [
        ParcelStatus::PICKUP_ASSIGN_CANCEL => 'pickup-man-assign-cancel',
        ParcelStatus::PICKUP_RE_SCHEDULE_CANCEL => 'pickup-reschedule-cancel',
        ParcelStatus::RECEIVED_BY_PICKUP_MAN_CANCEL => 'receved-by-pickupman-cancel',
        ParcelStatus::RECEIVED_WAREHOUSE_CANCEL => 'receved-warehouse-cancel',
        ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL => 'delivery-man-assign-cancel',
        ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL => 'delivery-re-schedule-cancel',
        ParcelStatus::TRANSFER_TO_HUB_CANCEL => 'transfer-to-hub-cancel',
        ParcelStatus::RECEIVED_BY_HUB_CANCEL => 'received-by-hub-cancel',
        ParcelStatus::RETURN_TO_COURIER_CANCEL => 'return-to-courier-cancel',
        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL => 'return-assign-to-merchant-cancel',
        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE_CANCEL => 'return-assign-re-schedule-merchant-cancel',
        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT_CANCEL => 'return-received-by-merchant-cancel',
        ParcelStatus::DELIVERED_CANCEL => 'delivered-cancel',
        ParcelStatus::PARTIAL_DELIVERED_CANCEL => 'partial-delivered-cancel',
    ];

    /**
     * Mapping cancel → titre data.
     */
    private const CANCEL_TITLE_MAP = [
        ParcelStatus::PICKUP_ASSIGN_CANCEL => 'pickup assign',
        ParcelStatus::PICKUP_RE_SCHEDULE_CANCEL => 'Pickup re-schedule',
        ParcelStatus::RECEIVED_BY_PICKUP_MAN_CANCEL => 'Received by pickup-man',
        ParcelStatus::RECEIVED_WAREHOUSE_CANCEL => 'Received warehouse',
        ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL => 'Delivery man assign',
        ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL => 'Delivery re-schedule',
        ParcelStatus::TRANSFER_TO_HUB_CANCEL => 'Transfer to hub',
        ParcelStatus::RECEIVED_BY_HUB_CANCEL => 'Received by hub',
        ParcelStatus::RETURN_TO_COURIER_CANCEL => 'Return to courier',
        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL => 'Return assign to merchant',
        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE_CANCEL => 'Return merchant Re-Schedule Cancel',
        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT_CANCEL => 'Return received by merchant',
        ParcelStatus::DELIVERED_CANCEL => 'Delivered cancel',
        ParcelStatus::PARTIAL_DELIVERED_CANCEL => 'Partial delivered cancel',
    ];

    /**
     * Retourne les statuts autorisés pour un colis donné.
     */
    public function getAllowedStatuses(Parcel $parcel): array
    {
        return self::STATUS_TRANSITIONS[$parcel->status] ?? [];
    }

    /**
     * Génère le HTML du dropdown de changement de statut.
     */
    public function buildStatusDropdown(Parcel $parcel): string
    {
        $allowedStatuses = $this->getAllowedStatuses($parcel);

        if (empty($allowedStatuses)) {
            return '';
        }

        $translations = trans('parcelStatus');
        $html = '';

        foreach ($allowedStatuses as $status) {
            $label = $translations[$status] ?? $status;

            if (in_array($status, array_keys(self::CANCEL_ROUTE_MAP))) {
                $html .= $this->buildCancelLink($status, $label, $parcel);
            } else {
                $html .= $this->buildActionLink($status, $label, $parcel);
            }
        }

        return $html;
    }

    /**
     * Génère un badge HTML pour un statut donné.
     */
    public function buildBadge(int $statusId): string
    {
        $cssClass = self::STATUS_BADGE_MAP[$statusId] ?? 'badge-secondary';
        $label = trans('parcelStatus.'.$statusId);

        return '<span class="badge badge-pill '.$cssClass.'">'.$label.'</span>';
    }

    /**
     * Retourne l'icône Font Awesome pour un statut.
     */
    public function getIcon(int $statusId): ?string
    {
        return self::STATUS_ICON_MAP[$statusId] ?? null;
    }

    /**
     * Génère le lien d'annulation pour un cancel status.
     */
    private function buildCancelLink(string $status, string $label, Parcel $parcel): string
    {
        $route = self::CANCEL_ROUTE_MAP[$status] ?? '#';
        $cssClass = self::CANCEL_CSS_MAP[$status] ?? '';
        $title = self::CANCEL_TITLE_MAP[$status] ?? $label;

        return '<a class="dropdown-item '.$cssClass.'" '
            .'data-title="'.e($title).'" '
            .'data-url="'.route($route).'" '
            .'data-parcel="'.$parcel->id.'" '
            .'href="#">'.$label.'</a>';
    }

    /**
     * Génère le lien d'action pour un status actif.
     */
    private function buildActionLink(string $status, string $label, Parcel $parcel): string
    {
        $cssClass = 'parcel-id';
        $extraData = '';

        if ($status === ParcelStatus::PICKUP_RE_SCHEDULE) {
            $cssClass .= ' parcel-id-pickup-man';
            $extraData = ' data-parcelstatus="'.ParcelStatus::PICKUP_ASSIGN.'"';
        } elseif ($status === ParcelStatus::TRANSFER_TO_HUB) {
            $cssClass .= ' parcel-id-transfer-hub';
        } elseif ($status === ParcelStatus::DELIVERY_RE_SCHEDULE) {
            $cssClass .= ' parcel-id-delivery-man';
            $extraData = ' data-parcelstatus="'.ParcelStatus::DELIVERY_MAN_ASSIGN.'"';
        } elseif ($status === ParcelStatus::RECEIVED_WAREHOUSE) {
            $cssClass .= ' received_warehouse';
            $extraData = ' data-hub="'.$parcel->hub_id.'" '
                .'data-url="'.route('parcel.received.warehouse.hub.select').'"';
        }

        return '<a class="dropdown-item '.$cssClass.'" '
            .'data-parcel="'.$parcel->id.'" '
            .$extraData.' '
            .'data-toggle="modal" '
            .'data-target="#parcelstatus'.$status.'" '
            .'href="#">'.$label.'</a>';
    }

    // ──────────────────────────────────────────────
    //  Todo Status
    // ──────────────────────────────────────────────

    private const TODO_TRANSITIONS = [
        TodoStatus::PENDING => [TodoStatus::PROCESSING],
        TodoStatus::PROCESSING => [TodoStatus::COMPLETED],
        TodoStatus::COMPLETED => [],
    ];

    /**
     * Génère le dropdown de statut pour un todo.
     */
    public function buildTodoStatusDropdown($todo): ?string
    {
        $allowedStatuses = self::TODO_TRANSITIONS[$todo->status] ?? [TodoStatus::PENDING];

        if (empty($allowedStatuses)) {
            return null;
        }

        $translations = trans('to_do');
        $html = '';

        foreach ($allowedStatuses as $status) {
            $label = $translations[$status] ?? $status;
            $routeName = match ($status) {
                TodoStatus::PENDING, TodoStatus::PROCESSING => 'todo.processing',
                TodoStatus::COMPLETED => 'todo.completed',
            };
            $cssClass = match ($status) {
                TodoStatus::PENDING => 'pending',
                TodoStatus::PROCESSING => 'processing',
                TodoStatus::COMPLETED => 'completed',
            };
            $target = $status === TodoStatus::COMPLETED
                ? '#todoStatus1'.$status
                : '#todoStatus'.$status;

            $html .= '<a class="dropdown-item '.$cssClass.'" '
                .'data-title="'.strtolower($label).'" '
                .'data-id="'.$todo->id.'" '
                .'id="todo_btn" '
                .'data-url="'.route($routeName).'" '
                .'data-toggle="modal" '
                .'data-target="'.$target.'" '
                .'href="#">'.$label.'</a>';
        }

        return $html;
    }
}
