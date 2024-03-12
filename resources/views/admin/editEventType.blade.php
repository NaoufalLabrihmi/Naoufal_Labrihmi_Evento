@extends('admin.layout.main')

@section('content')
<div class="container">
    <h2>Update Event Type</h2>
    <form action="{{ route('admin.updateEventType', $eventType->event_type_id) }}" method="POST">
        @csrf
        @method('PUT') <!-- Add this line to specify the PUT method -->
        <div class="form-group">
            <label for="eventTypeName">Event Type Name</label>
            <input type="text" class="form-control" id="eventTypeName" name="edit_eventTypeName" value="{{ $eventType->event_type_name }}">
        </div>
        <button type="submit" class="btn btn-primary">Update Event Type</button>
    </form>
</div>

@endsection