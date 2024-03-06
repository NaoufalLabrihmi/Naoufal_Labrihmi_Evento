@extends('layout.main')
@section('content')

<div class="container mt-5">
    <div class="heading mb-5">
        <h2>Forgot Password</h2>
        <p>Enter your email address to receive a password reset link.</p>
    </div>

    <div class="row mb-5 pb-5">
        <div class="col-md-8 col-12 m-auto">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">
                        Send Password Reset Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection