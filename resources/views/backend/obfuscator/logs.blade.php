@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('menus.backend.obfuscator.logs'))

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
                        <th class="text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($rows->count() > 0)
                        @foreach($rows->items() as $activity)
                            <tr class="bg-{{$activity['color']}}">
                                {{-- {{dd($activity)}} --}}
                                @foreach($activity as $key => $value)
                                    @if(in_array($key, ['description','created_at']))
                                        <td class="{{ $key !== 'created_at' ? 'text-left' : 'text-center' }}">
                                            @if ($key == 'created_at')
                                                <a href="{{ route('log-viewer::logs.show', [$value]) }}" class="btn btn-sm btn-primary">
                                                    {{ $value }}
                                                </a>
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
            <div class="card">
                <div class="card-header">
                    <strong>{{__('menus.backend.obfuscator.logs')}}</strong>
                </div><!--card-header-->
                <div class="card-block">
                    
                </div><!--card-block-->
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
    <div class="row">
        <div class="col">
            {!! $rows->render('log-viewer::_pagination.bootstrap-4') !!}
        </div>
    </div>
@endsection
