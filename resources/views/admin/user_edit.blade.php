@extends('admin.layout.main')

@section('content')

<div class="container mt-5">
    <div class="heading mb-5">
        <h2>Edit User Profile</h2>
    </div>
    <div class="row mb-5 pb-5">
        <div class="col-md-8 col-12 m-auto">
            <div id="edit_errList"></div>
            <form action="{{ route('admin_user_update', $user->id) }}" method="post" id="editForm">
                @csrf
                <div class="mb-3">
                    <label for="editname" class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="editname" id="editname" value="{{ $user->name }}" placeholder="Enter Your Name">
                </div>

                <div class="mb-3">
                    <label for="editemail" class="form-label">Email</label>
                    <input type="email" class="form-control" name="editemail" id="editemail" value="{{ $user->email }}" placeholder="Enter Your Email">
                </div>
                <div class="mb-3">
                    <label for="editusertype" class="form-label">User Type</label>
                    <select class="form-control" name="editusertype" id="editusertype">
                        <option value="A" @if($user->user_type == 'A') selected @endif>Admin</option>
                        <option value="U" @if($user->user_type == 'U') selected @endif>Regular User</option>
                        <option value="OA" @if($user->user_type == 'OA') selected @endif>Organisateur</option>
                    </select>
                </div>


                <div class="mb-3">
                    <label for="editcontact" class="form-label">Contact</label>
                    <input type="text" class="form-control" name="editcontact" id="editcontact" value="{{ $user->contact }}" placeholder="Enter Your Contact">
                </div>

                <div class="mb-3">
                    <label for="editaddress" class="form-label">Address</label>
                    <input type="text" class="form-control" name="editaddress" id="editaddress" value="{{ $user->address }}" placeholder="Enter Your Address">
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary update-btn">Update</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection