@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        Edit Site
                        <a class="btn btn-danger ml-auto" href="#" onclick="event.preventDefault();
                            document.getElementById('delete-site').submit();">Delete</a>
                        <form id="delete-site" action="{{ route('sites.destroy', $site) }}" method="POST" style="display: none;">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                        </form>
                    </div>

                    <div class="card-body">

                        @if (session('discovery') === 'fail')
                            <div class="alert alert-warning" role="alert">
                                Automatic discovery has failed. Please manually enter the information below.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('sites.update', $site) }}">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ $site->name }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="url" class="col-md-4 col-form-label">URL</label>

                                <div class="col-md-6">
                                    <input id="url" type="url" class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}" name="url" value="{{ $site->url }}" required>

                                    @if ($errors->has('url'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="root_uri" class="col-md-4 col-form-label">Root URI</label>

                                <div class="col-md-6">
                                    <input id="root_uri" type="url" class="form-control{{ $errors->has('root_uri') ? ' is-invalid' : '' }}" name="root_uri" value="{{ $site->root_uri }}" required>

                                    @if ($errors->has('root_uri'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('root_uri') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
