@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center">
                        {{ $site->name }}
                        <a class="btn btn-info ml-auto" href="{{ route('sites.edit', $site) }}">Edit</a>
                    </div>

                    <div class="card-body">
                        @if($status)
                            <div class="alert alert-danger" role="alert">
                                {{ $status }}
                            </div>
                        @endif
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
                                <p>##</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        Plugins
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Version</th>
                                <th scope="col">Activated</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th scope="row">NAME</th>
                                <td>1.0.0</td>
                                <td>TRUE</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
