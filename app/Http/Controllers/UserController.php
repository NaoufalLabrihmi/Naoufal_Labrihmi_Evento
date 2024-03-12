<?php

namespace App\Http\Controllers;

use App\Custom;
use App\Models\Admin;
use App\Models\Followers;
use App\Models\Notification;
use App\Models\Organizer;
use App\Models\User;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function index()
    {
        $users = DB::table('users')
            ->select('id', 'name', 'email', 'contact', 'address')
            ->where('user_type', '=', 'U')
            ->orderBy('id', 'desc')
            ->union(
                DB::table('organizers')
                    ->select('id', 'name', 'email', 'contact', 'address')
                    ->where('user_type', '=', 'U')
            )
            ->union(
                DB::table('admins')
                    ->select('id', 'name', 'email', 'contact', 'address')
                    ->where('user_type', '=', 'U')
            )
            ->paginate(10);
        $data = compact('users');
        return view('admin.users')->with($data);
    }

    public function organizations()
    {
        // Fetch users from the 'users' table
        $users = Users::where('user_type', '=', 'OA')->get();

        // Fetch organizers from the 'organizers' table
        $organizers = Organizer::where('user_type', '=', 'OA')->get();

        // Combine the users and organizers into a single collection
        $allOrganizers = $users->merge($organizers);

        // Pass the combined data to the view
        return view('admin.organization', compact('allOrganizers'));
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


    public function admin_org_register(Request $request)
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

        // Create a new organizer instance and save it to the database
        $organizer = new \App\Models\Organizer(); // Adjust the namespace as per your folder structure
        $organizer->name = $request->input('name');
        $organizer->org_name = $request->input('org_name');
        $organizer->email = $request->input('email');
        $organizer->contact = $request->input('contact');
        $organizer->address = $request->input('address');
        $organizer->user_type = $request->input('org_type', 'OA'); // Set default if not provided
        $organizer->password = Hash::make($request->input('password')); // Use Hash::make for password hashing
        $organizer->save();

        // Redirect back with a success message
        return redirect()->route('admin.organization')->with('success', 'Organizer has been registered successfully.');
    }



    public function registerForm()
    {
        return view('admin.register'); // Assuming you have a view file named register.blade.php
    }

    public function registerFormOrganizateur()
    {
        return view('admin.registerOrg'); // Assuming you have a view file named register.blade.php
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
        $user = User::where('user_type', 'U')->find($id) ? User::find($id) : Organizer::find($id);
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
            // Find the user by ID
            $user = User::where('user_type', 'U')->find($id) ? User::find($id) : Organizer::find($id);

            // Check if the user exists
            if (!$user) {
                return redirect()->back()->with('error', 'User not found.');
            }

            // Update user data
            $user->name = $request['editname'];
            $user->email = $request['editemail'];
            $user->contact = $request['editcontact'];
            $user->address = $request['editaddress'];
            $user->user_type = $request->input('editusertype'); // Update user type

            // Save the updated user
            $user->save();

            return redirect()->route('admin.users')->with('success', 'User has been updated successfully.');
        }
    }



    public function editOrganizer($id)
    {
        // Fetch the organizer from the organizers table
        $user = Organizer::where('user_type', 'OA')->find($id) ? Organizer::find($id) : Users::find($id);

        // Pass the organizer data to the view
        return view('admin.editOrg', compact('user'));
    }
    public function updateOrganizer(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'user_type' => 'required|in:A,U,OA',
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find the organizer by ID
        $organizer = Organizer::find($id);
        if (!$organizer) {
            // If organizer is not found, check if it's a user
            $organizer = User::find($id);
            if (!$organizer) {
                // If not found, check if it's an admin
                $organizer = Admin::find($id);
            }
        }

        // If organizer not found, return error
        if (!$organizer) {
            return redirect()->back()->with('error', 'Organizer not found.');
        }

        // Update organizer data
        $organizer->name = $request->input('name');
        $organizer->user_type = $request->input('user_type'); // Update user type
        $organizer->email = $request->input('email');
        $organizer->org_name = $request->input('org_name');
        $organizer->contact = $request->input('contact');
        $organizer->address = $request->input('address');
        // Save the updated organizer
        $organizer->save();

        // Redirect back with success message
        return redirect()->route('admin.organization')->with('success', 'Organizer updated successfully.');
    }


    public function admin_user_delete($id)
    {
        $user = User::where('user_type', 'U')->find($id) ? User::find($id) : Organizer::find($id);
        if (!$user) {
            $user = Admin::find($id);
        }
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $user->delete(); // Soft delete

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function admin_org_delete($id)
    {
        $user = Organizer::where('user_type', 'OA')->find($id) ? Organizer::find($id) : Users::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'Organizer not found.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Organizer deleted successfully.');
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
