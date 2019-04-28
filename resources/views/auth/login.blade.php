@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <login-form
                        :old-input='@json(old())'
                        :validation-errors='@json($errors->messages())'
                    ></login-form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
