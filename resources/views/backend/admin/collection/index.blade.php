@extends('backend.partials.master')
@section('title', 'Gestion des Collectes')
@section('maincontent')

<div class="container-fluid dashboard-content">

    {{-- Header --}}
    <div class="gc-greeting">
        <div>
            <h1><i class="fas fa-truck-loading"></i> Collectes</h1>
            <p>Suivi et gestion des collectes en temps réel</p>
        </div>
        <a href="{{ route('admin.collection.map') }}" class="btn btn-primary" style="border-radius:12px;">
            <i class="fas fa-map-marked-alt"></i> Carte temps réel
        </a>
    </div>

    {{-- Stats --}}
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #6c757d;">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-0">Total</h6>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #ffc107;">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-0">En attente</h6>
                    <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #17a2b8;">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-0">Actives</h6>
                    <h3 class="mb-0 text-info">{{ $stats['active'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #28a745;">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-0">Terminées</h6>
                    <h3 class="mb-0 text-success">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="small text-muted">Statut</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">Tous</option>
                        <option value="1" {{ request('status') == 1 ? 'selected' : '' }}>En attente</option>
                        <option value="2" {{ request('status') == 2 ? 'selected' : '' }}>Assignée</option>
                        <option value="3" {{ request('status') == 3 ? 'selected' : '' }}>En ramassage</option>
                        <option value="4" {{ request('status') == 4 ? 'selected' : '' }}>Collectée</option>
                        <option value="5" {{ request('status') == 5 ? 'selected' : '' }}>En transit</option>
                        <option value="6" {{ request('status') == 6 ? 'selected' : '' }}>Terminée</option>
                        <option value="7" {{ request('status') == 7 ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small text-muted">Date</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small text-muted">Marchand</label>
                    <select name="merchant_id" class="form-control form-control-sm">
                        <option value="">Tous</option>
                        @foreach($merchants as $m)
                        <option value="{{ $m->id }}" {{ request('merchant_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->business_name ?? $m->user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-primary btn-sm mr-2"><i class="fas fa-filter"></i> Filtrer</button>
                    <a href="{{ route('admin.collection.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- Liste --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($collections->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Marchand</th>
                            <th>Colis</th>
                            <th>Cash</th>
                            <th>Livreur</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($collections as $c)
                        <tr>
                            <td><strong>#{{ $c->id }}</strong></td>
                            <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $c->merchant->business_name ?? $c->merchant->user->name ?? '—' }}</td>
                            <td><span class="badge badge-light">{{ $c->parcel_count }}</span></td>
                            <td>{{ number_format($c->total_cash_collection, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $c->deliveryMan?->user?->name ?? '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $c->status_color }} badge-pill">
                                    {{ $c->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.collection.show', $c->id) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($c->status == 1 && !$c->delivery_man_id)
                                <button class="btn btn-sm btn-outline-success assign-btn"
                                        data-id="{{ $c->id }}"
                                        data-toggle="modal"
                                        data-target="#assignModal"
                                        title="Affecter">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $collections->withQueryString()->links() }}
            @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-inbox fa-2x mb-3"></i>
                <p>Aucune collecte trouvée</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Affectation --}}
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="assignForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Affecter un livreur</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Livreur disponible</label>
                        <select name="delivery_man_id" class="form-control" required>
                            <option value="">Sélectionner...</option>
                            @foreach($deliverymen as $dm)
                            @if($dm->is_available)
                            <option value="{{ $dm->id }}">
                                {{ $dm->user->name }} — {{ $dm->user->mobile ?? '' }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Affecter</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
document.querySelectorAll('.assign-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('assignForm').action = '/admin/collection/' + id + '/assign';
    });
});
</script>
@endsection
