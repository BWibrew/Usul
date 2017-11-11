@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">New Site</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('sites.store') }}">
                            {{ csrf_field() }}

                            <div class="form-group row">
                                <label for="url" class="col-md-4 col-form-label">Site URL</label>

                                <div class="col-md-6">
                                    <input id="url" type="url" class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}" name="url" value="{{ old('url') }}" required autofocus>

                                    @if ($errors->has('url'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Add
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
