<!-- resources/views/admin/eventTypes/edit.blade.php -->
@extends('admin.layout.main')

@section('content')
<div class="container mt-3">
    <h2>Edit Event Type</h2>
    <form action="{{ route('admin_eventType_update', $eventType->id) }}" method="POST">
        @csrf
        @method('POST')
        <div class="mb-3">
            <label for="eventTypeName" class="form-label">Event Type Name</label>
            <input type="text" class="form-control" name="eventTypeName" id="eventTypeName" value="{{ $eventType->event_type_name }}">
        </div>
        <button type="submit" class="btn btn-primary">Update Event Type</button>
    </form>
</div>
@endsection