@extends('backend.partials.master')
@section('title')
    {{ __('support.supprot') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('support.supprot') }}</h1>
            <p class="wc-page-subtitle">{{ __('support.supprot_list') }} · vos demandes d'assistance</p>
        </div>
        <div class="wc-toolbar">
            <a href="{{route('merchant-panel.support.add')}}" class="wc-btn wc-btn-primary wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('support.supprot_add') }}"><i class="fa fa-plus"></i> {{ __('support.supprot_add') }}</a>
        </div>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('support.supprot_list') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Suivi de vos tickets.</p>
                </div>
            </div>
        </div>

        @if(count($supports) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-headset"></i></div>
                <p class="wc-empty-title">Aucun ticket de support</p>
                <p class="wc-empty-description">Créez un ticket pour contacter l'équipe d'assistance.</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('support.sl') }}</th>
                            <th>{{ __('support.user_info') }}</th>
                            <th>{{ __('support.subject') }}</th>
                            <th>{{ __('support.date') }}</th>
                            <th>{{ __('levels.status') }}</th>
                            <th class="text-right">{{ __('support.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($supports as $support)
                        <tr>
                            <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                            <td>
                                <div class="text-[12.5px] leading-relaxed text-wc-ink-2">
                                    <span class="font-bold text-wc-ink">{{ $support->user->name}}</span><br/>
                                    {{ $support->user->email }}<br/>
                                    <span class="text-wc-muted">{{ $support->department->title }}</span> · {{$support->service }}
                                </div>
                            </td>
                            <td>
                                <a class="text-wc-primary font-bold text-[13px]" href="{{route('merchant-panel.support.view',$support->id)}}" data-toggle="tooltip" data-placement="top" title="{{ __('levels.view') }}">{{$support->subject }}</a>
                            </td>
                            <td class="text-wc-muted-2 whitespace-nowrap">{{dateFormat($support->date) }}</td>
                            <td>{!! $support->my_status !!}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{route('merchant-panel.support.view',$support->id)}}" class="wc-btn wc-btn-outline wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.view') }}"><i class="fas fa-eye"></i> {{ __('levels.view') }}</a>
                                    <a href="{{route('merchant-panel.support.edit',$support->id)}}" class="wc-btn wc-btn-outline wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.edit') }}"><i class="fas fa-edit"></i> {{ __('levels.edit') }}</a>
                                    <form id="delete" value="Test" action="{{route('merchant-panel.support.delete',$support->id)}}" method="POST" data-title="{{ __('delete.support') }}" class="m-0">
                                        @method('DELETE')
                                        @csrf
                                        <input type="hidden" name="" value="{{ __('support.title') }}" id="deleteTitle">
                                        <button type="submit" class="wc-btn wc-btn-danger-soft wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.delete') }}"><i class="fa fa-trash"></i> {{ __('levels.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $supports->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $supports->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $supports->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $supports->links() }}</span>
            </div>
        @endif
    </div>
</div>
@endsection()