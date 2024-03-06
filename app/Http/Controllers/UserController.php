<?php

namespace App\Http\Controllers;

use App\Custom;
use App\Models\Followers;
use App\Models\Notification;
use App\Models\User;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = Users::orderBy('id', 'desc')->where('user_type', '=', 'U')->paginate(10);
        $data = compact('users');
        return view('admin.users')->with($data);
    }

    public function organizations()
    {
        $users = Users::where('user_type', '=', 'OA')->get();
        $data = compact('users');
        return view('admin.organization')->with($data);
    }

    public function status_check(Request $request)
    {
        $user = Users::find($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $user->status = $request->status;
        $user->save();

        return response()->json(['user' => $user], 200);
    }




    public function admin_user_register(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'org_name' => 'nullable|string',
            'contact' => 'nullable|string',
            'address' => 'nullable|string',
            'org_type' => 'nullable|string',
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create a new user instance and save it to the database
        $user = new Users;
        $user->name = $request->input('name');
        $user->org_name = $request->input('org_name');
        $user->email = $request->input('email');
        $user->contact = $request->input('contact');
        $user->address = $request->input('address');
        $user->user_type = $request->input('org_type', 'U'); // Set default if not provided
        $user->password = Hash::make($request->input('password')); // Use Hash::make for password hashing
        $user->save();

        // Redirect back with a success message
        return redirect()->route('admin.users')->with('success', 'User has been registered successfully.');
    }

    public function registerForm()
    {
        return view('admin.register'); // Assuming you have a view file named register.blade.php
    }



    public function admin_user_edit(Request $request)
    {
        $user_id = $request->user_id;
        $user = Users::find($user_id);
        return response()->json([
            'user' => $user,
        ]);
    }
    public function admin_user_edit_view($id)
    {
        $user = Users::find($id);
        return view('admin.user_edit')->with('user', $user);
    }

    public function admin_user_update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'editname' => 'required',
            'editemail' => 'required|email',
            'editusertype' => 'required|in:A,U,OA',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {

            $user = Users::find($id);
            $user->name = $request['editname'];
            $user->email = $request['editemail'];
            $user->contact = $request['editcontact'];
            $user->address = $request['editaddress'];
            $user->user_type = $request->input('editusertype'); // Update user type
            $user->update();
            return redirect()->route('admin.users')->with('success', 'User has been updated successfully.');
        }
    }

    public function admin_user_delete($id)
    {
        $user = Users::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $user->delete(); // Soft delete

        return redirect()->back()->with('success', 'User deleted successfully.');
    }


    // public function event_notifications_for_follow_users(){
    //Get All Notifications
    // $allNotifications = Notification::orderBy('noti_id', 'desc')->where('noti_type', '=', 'E')->where('noti_for', '=', 'U')->paginate(10);
    // $allNotifications = Notification::orderBy('noti_id', 'desc')->paginate(10);
    // //Get Organizers Id Fron Notifications
    //  $org_id = $allNotifications->pluck('noti_byId');
    //  //user Must Be Follow That Organizer
    //  foreach($org_id as $value) {
    //  $follow_check[] = Followers::where('user_id', '=', session()->get('user_id'))->where('organizer_id', '=', $value)->first();
    //  }

    // //  check($follow_check);

    // //Get All Notifications
    // // $find_notification = array();
    //  foreach ($follow_check as $key => $value) {
    //       // if ($key == 3) {
    //       //   checkArray($value);
    //       // }
    //    $find_notification[] = Notification::orderBy('noti_id', 'desc')->where('noti_byId', '=', $value->organizer_id ?? '')->where('created_at', '>', $value->created_at ?? '')->pluck('noti_id');
    //   }
    //   // var_dump($final_notification);
    //  $final_notification[] = array_filter($find_notification);
    //   // check($final_notification);

    // $data = compact('final_notification');
    // return view('notifications')->with($data);

    //   $data = compact('allNotifications');
    //   return view('notifications')->with($data);
    // }



}
