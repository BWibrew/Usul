@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                @if(session('status'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        Authentication Settings
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('sites.auth', $site) }}">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}
                            <div class="form-group row">
                                <label for="currentType" class="col-md-4 col-form-label">Current Authentication</label>
                                <div class="col-md-6">
                                    <input id="currentType" type="text" class="form-control-plaintext" name="currentType" value="{{ $site->auth_type ?: 'Not Authenticated' }}" readonly>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label for="type" class="col-md-4 col-form-label">Type</label>
                                <div class="col-md-6">
                                    <select id="type" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="type" value="{{ old('username') }}">
                                        <option>JWT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="username" class="col-md-4 col-form-label">Username</label>
                                <div class="col-md-6">
                                    <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required>

                                    @if ($errors->has('username'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-success">Re-Authenticate</button>
                                    <a class="btn btn-outline-secondary ml-3" href="{{ route('sites.show', $site) }}">Back</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
