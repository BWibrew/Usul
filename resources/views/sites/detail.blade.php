@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                @isset($status['wp_rest'])
                    <div class="alert alert-danger" role="alert">
                        {{ $status['wp_rest'] }}
                    </div>
                @endisset
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center">
                        {{ $site->name }}
                        <a class="btn btn-info ml-auto" href="{{ route('sites.edit', $site) }}">Edit</a>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>URL:</strong></p>
                            </div>
                            <div class="col-md-9">
                                <a href="{{ $site->url }}" target="_blank">{{ $site->url }}</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>API root:</strong></p>
                            </div>
                            <div class="col-md-9">
                                <p>{{ $site->root_uri }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>WP Version:</strong></p>
                            </div>
                            <div class="col-md-9">
                                <p>{{ $wpVersion }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center">
                        Connection
                        <a class="btn btn-info ml-auto" href="{{ route('sites.editAuth', $site) }}">Authentication</a>
                    </div>

                    <div class="card-body">
                        @isset($status['version'])
                            <div class="alert alert-danger" role="alert">
                                {{ $status['version'] }}
                            </div>
                        @endisset
                        @isset($status['namespaces'])
                            <div class="alert alert-danger" role="alert">
                                {{ $status['namespaces'] }}
                            </div>
                        @endisset
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <span><strong>WordPress REST API:</strong></span>
                            </div>
                            <div class="col-md-8">
                                <span class="p-1 mb-2 text-white {{ $isConnected && $connection['wp_rest'] ? 'bg-success' : 'bg-danger' }}">{{ $isConnected && $connection['wp_rest'] ? 'Successful!' : 'Not Successful!' }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <span><strong>Authenticated:</strong></span>
                            </div>
                            <div class="col-md-8">
                                <span class="p-1 mb-2 text-white {{ $isConnected && $connection['authenticated'] ? 'bg-success' : 'bg-danger' }}">{{ $isConnected && $connection['authenticated'] ? 'Successful!' : 'Not Successful!' }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <span><strong>WP Site Monitor:</strong></span>
                            </div>
                            <div class="col-md-8">
                                <span class="p-1 mb-2 text-white {{ $isConnected && $connection['site_monitor'] ? 'bg-success' : 'bg-danger' }}">{{ $isConnected && $connection['site_monitor'] ? 'Successful!' : 'Not Successful!' }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <span><strong>API Namespaces:</strong></span>
                            </div>
                            <div class="col-md-8">
                                <ul>
                                    @isset($namespaces)
                                        @foreach($namespaces as $namespace)
                                            <li>{{ $namespace }}</li>
                                        @endforeach
                                    @endisset
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">
                        Plugins
                    </div>
                    <div class="card-body">
                        @isset($status['plugins'])
                            <div class="alert alert-danger" role="alert">
                                {{ $status['plugins'] }}
                            </div>
                        @endisset
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Version</th>
                                <th scope="col">Activated</th>
                            </tr>
                            </thead>
                            <tbody>
                            @isset($plugins)
                                @foreach($plugins as $plugin => $details)
                                    <tr>
                                        <th scope="row">{{ $details['Name'] }}</th>
                                        <td>{{ $details['Version'] }}</td>
                                        <td class="{{ $details['Active'] ? '' : 'table-warning' }}">{{ $details['Active'] ? 'True' : 'False' }}</td>
                                    </tr>
                                @endforeach
                            @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
