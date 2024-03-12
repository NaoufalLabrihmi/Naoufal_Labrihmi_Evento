@extends('admin.layout.main')

@section('content')

<div class="container mt-5">
    <div class="heading mb-5">
        <h2>Add Organizateur</h2>
    </div>
    <div class="row mb-5 pb-5">
        <div class="col-md-8 col-12 m-auto">
            <form action="{{ route('admin.organizations') }}" method="post" id="registerform">
                @csrf
                <div class="mb-3">
                    <input type="hidden" name="org_type" value="OA">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" id="name" aria-describedby="helpId" placeholder="">
                    <small id="helpId" class="form-text text-muted">@error('name')
                        {{ $message }}
                        @enderror</small>
                </div>
                <div class="mb-3">
                    <input type="hidden" name="org_type" value="OA">
                    <label for="org_name" class="form-label">Organizer Name</label>
                    <input type="text" class="form-control" name="org_name" id="org_name" aria-describedby="helpId" placeholder="">
                    <small id="helpId" class="form-text text-muted">@error('org_name')
                        {{ $message }}
                        @enderror</small>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" aria-describedby="helpId" placeholder="">
                    <small id="helpId" class="form-text text-muted">@error('email')
                        {{ $message }}
                        @enderror</small>
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="text" class="form-control" name="contact" id="contact" aria-describedby="helpId" placeholder="">
                    <small id="helpId" class="form-text text-muted">@error('contact')
                        {{ $message }}
                        @enderror</small>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Home/Shop Address</label>
                    <input type="text" class="form-control" name="address" id="address" aria-describedby="helpId" placeholder="Enter Your Address">
                    <small id="helpId" class="form-text text-muted">@error('address')
                        {{ $message }}
                        @enderror</small>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" aria-describedby="helpId" placeholder="Enter Your Password">
                    <small id="helpId" class="form-text text-muted">@error('password')
                        {{ $message }}
                        @enderror</small>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" aria-describedby="helpId" placeholder="Confirm Password">
                    <small id="helpId" class="form-text text-muted">@error('password_confirmation')
                        {{ $message }}
                        @enderror</small>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary register-btn">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection