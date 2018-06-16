@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('menus.backend.obfuscator.logs'))

@push('after-styles')
    @include('log-viewer::_template.style')
@endpush

@section('page-header')
    <h5 class="mb-4">Obfuscator Logs
        <small class="text-muted">by <a href="https://ronanversendaal.com" target="_blank">Ronan Versendaal</a></small>
    </h5>
@endsection


@section('content')

    <div class="row">
        <div class="col">
            {!! $rows->render('log-viewer::_pagination.bootstrap-4') !!}
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ __('menus.backend.obfuscator.logs') }}
        </div><!-- box-header -->

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Log description</th>
                        <th>Logged at</th>
                        <th>Log level</th>
                        <th class="text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($rows->count() > 0)
                        @foreach($rows->items() as $activity)
                            <tr>
                                {{-- {{dd($activity)}} --}}
                                @foreach($activity as $key => $value)
                                    @if(in_array($key, ['description','created_at', 'properties']))
                                        <td class="{{ !in_array($key, ['created_at', 'properties']) ? 'text-left' : 'text-center' }} dont-break-out">
                                            @if ($key == 'created_at')
                                                <a href="{{ route('log-viewer::logs.show', [$value]) }}" class="btn btn-sm btn-primary">
                                                    {{ $value }}
                                                </a>
                                            @elseif($key == 'properties')
                                                <span class="badge level level-{{$activity['color']}}">{{$activity['properties']->get('level')}}</span>
                                            @else
                                                <span class="{{ $key }}">{{ $value }}</span>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Log Viewer Actions">
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-backdrop="false" data-target="#delete-log-modal" data-log-date="{{ $activity['created_at'] }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" class="text-center">
                                <span class="badge badge-default">{{ __('log-viewer::general.empty-logs') }}</span>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div><!--table-responsive-->
        </div>
    </div>
    <div class="row">
        <div class="col">
            {!! $rows->render('log-viewer::_pagination.bootstrap-4') !!}
        </div>
    </div>
@endsection
