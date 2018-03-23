@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        Authentication Settings
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('sites.auth', $site) }}">
                            {{ csrf_field() }}

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <a class="btn btn-outline-secondary ml-3" href="{{ route('sites.show', $site) }}">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
