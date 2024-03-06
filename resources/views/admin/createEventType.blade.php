@extends('admin.layout.main')

@section('content')

<div class="container mt-3">
    <h2>Create New Event Type</h2>
    <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Iusto nemo totam commodi, dolorem quia quaerat mollitia sint nulla recusandae quam!</p>
</div>

<div class="container">
    <form action="{{ route('eventTypes_register') }}" method="post">
        @csrf
        <div class="row mb-5">
            <div class="col-md-6 col-12 mb-3">
                <label for="eventTypeName">Event Type Name</label>
                <input type="text" name="eventTypeName" id="eventTypeName" class="form-control" placeholder="Enter Event Type Name">
                @error('eventTypeName')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="container mt-5 mb-5 pb-5 pt-3">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

@endsection