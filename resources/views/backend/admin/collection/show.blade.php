@extends('backend.partials.master')
@section('title', 'Détails Collecte #' . $collection->id)
@section('maincontent')

<div class="container-fluid dashboard-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.collection.index') }}" class="text-muted"><i class="fas fa-arrow-left"></i> Retour</a>
            <h2 class="mt-1">Collecte #{{ $collection->id }}</h2>
        </div>
        <div>
            <span class="badge badge-{{ $collection->status_color }} badge-pill px-3 py-2" style="font-size:1rem;">
                {{ $collection->status_label }}
            </span>
        </div>
    </div>

    <div class="row">
        {{-- Infos --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0">Informations</h6></div>
                <div class="card-body">
                    <p><strong>Marchand :</strong> {{ $collection->merchant->business_name ?? $collection->merchant->user->name }}</p>
                    <p><strong>Date :</strong> {{ $collection->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Adresse :</strong> {{ $collection->pickup_address ?? '—' }}</p>
                    <p><strong>Note :</strong> {{ $collection->note ?? '—' }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0">Montants</h6></div>
                <div class="card-body">
                    <p><strong>Colis :</strong> {{ $collection->parcel_count }}</p>
                    <p><strong>Cash total :</strong> {{ number_format($collection->total_cash_collection, 0, ',', ' ') }} FCFA</p>
                    <p><strong>Frais total :</strong> {{ number_format($collection->total_delivery_amount, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>

            @if($collection->deliveryMan)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0">Livreur</h6></div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white p-2 mr-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <strong>{{ $collection->deliveryMan->user->name }}</strong>
                            <br><small class="text-muted">{{ $collection->deliveryMan->user->mobile ?? '' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Actions + Colis --}}
        <div class="col-md-8">
            {{-- Actions rapides --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0">Actions</h6></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.collection.status', $collection->id) }}" class="d-inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="3">
                        <button type="submit" class="btn btn-outline-primary btn-sm mr-2" {{ $collection->status != 2 ? 'disabled' : '' }}>
                            <i class="fas fa-truck"></i> Démarrer ramassage
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.collection.status', $collection->id) }}" class="d-inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="4">
                        <button type="submit" class="btn btn-outline-info btn-sm mr-2" {{ $collection->status != 3 ? 'disabled' : '' }}>
                            <i class="fas fa-check"></i> Collectée
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.collection.status', $collection->id) }}" class="d-inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="6">
                        <button type="submit" class="btn btn-success btn-sm mr-2" {{ $collection->status < 4 ? 'disabled' : '' }}>
                            <i class="fas fa-flag-checkered"></i> Terminer
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.collection.status', $collection->id) }}" class="d-inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="7">
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Annuler ?')">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </form>
                </div>
            </div>

            {{-- Colis --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0">Colis ({{ $collection->parcels->count() }})</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tracking</th>
                                    <th>Destinataire</th>
                                    <th>Cash</th>
                                    <th>Frais</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collection->parcels as $p)
                                <tr>
                                    <td><code>{{ $p->tracking_id }}</code></td>
                                    <td>{{ $p->customer_name }}</td>
                                    <td>{{ number_format($p->cash_collection, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($p->total_delivery_amount, 0, ',', ' ') }}</td>
                                    <td><span class="badge badge-{{ $p->status == 9 ? 'success' : 'secondary' }}">{{ __('parcelStatus.' . $p->status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Suivi de caisse --}}
            @if($collection->cashTrackings->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">Suivi de caisse COD</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Colis</th>
                                    <th>Attendu</th>
                                    <th>Encaissé</th>
                                    <th>Remis</th>
                                    <th>Restant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collection->cashTrackings as $ct)
                                <tr>
                                    <td><code>{{ $ct->parcel?->tracking_id }}</code></td>
                                    <td>{{ number_format($ct->amount_expected, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($ct->amount_collected, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($ct->amount_handed_over, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($ct->amount_remaining, 0, ',', ' ') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $ct->status_color }} badge-pill">
                                            {{ $ct->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
