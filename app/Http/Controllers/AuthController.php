<?php

namespace App\Http\Controllers;

use App\Custom;
use App\Models\Admin;
use App\Models\Notification;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{

    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function register_check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required| confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        } else {
            $user = new Users;
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->user_type = 'U';
            $user->username = Custom::slug($request['name']) . '-' . time();
            $user->save();

            $useNewId = $user->id;
            $notification = new Notification;
            $notification->noti_title = 'New user: ' . $request['name'] . ' has been registered successfully.';
            $notification->noti_for = 'A';
            $notification->noti_forId = '1';
            $notification->noti_type = 'Reg';
            $notification->noti_typeId = $useNewId;
            $notification->noti_byId = $useNewId;
            $notification->save();
            return response()->json(['success' => 'You have been Register Successfully. Login now']);
        }
    }

    public function login_check(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }

        // Attempt login for different guards
        if (Auth::guard('web')->attempt(['email' => $request['email'], 'password' => $request['password']])) {
            // Handle regular user login
            $user = Auth::guard('web')->user();
            if ($user->user_type == 'U') {
                // Set session variables for regular users
                session()->put('user_id', $user->id);
                session()->put('user_name', $user->name);
                session()->put('org_name', $user->org_name);
                session()->put('user_email', $user->email);
                session()->put('user_type', 'U');
            } elseif ($user->user_type == 'OA') {
                // Set session variables for organizers
                session()->put('user_id', $user->id);
                session()->put('user_name', $user->name);
                session()->put('org_name', $user->org_name);
                session()->put('user_email', $user->email);
                session()->put('user_type', 'OA');
            }
            return response()->json(['success']);
        } elseif (Auth::guard('admin')->attempt(['email' => $request['email'], 'password' => $request['password']])) {
            // Handle admin login
            $user = Auth::guard('admin')->user();
            // Set session variables for admins
            session()->put('admin_id', $user->id);
            session()->put('admin_name', $user->name);
            session()->put('admin_email', $user->email);
            session()->put('event_author_id', $user->id); // Set the event_author_id for admin
            session()->put('user_type', 'A');
            return response()->json(['success']);
        } elseif (Auth::guard('organizer')->attempt(['email' => $request['email'], 'password' => $request['password']])) {
            // Handle organizer login
            $user = Auth::guard('organizer')->user();
            // Set session variables for organizers
            session()->put('user_id', $user->id);
            session()->put('user_name', $user->name);
            session()->put('org_name', $user->org_name);
            session()->put('user_email', $user->email);
            session()->put('user_type', 'OA'); // Assuming 'OA' is the user_type for organizers
            return response()->json(['success']);
        } else {
            // Login attempt failed
            return response()->json([0]);
        }
    }



    public function org_register()
    {
        return view('auth.orgRegister');
    }

    public function org_register_check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'orgName' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'contact' => 'required',
            'password' => 'required| confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        } else {
            $user = new User;
            $user->name = $request['name'];
            $user->org_name = $request['orgName'];
            $user->email = $request['email'];
            $user->address = $request['address'];
            $user->contact = $request['contact'];
            $user->website = $request['website'];
            $user->username = Custom::slug($request['name']) . '-' . time();
            $user->password = Hash::make($request['password']);
            $user->user_type = 'OA';
            $user->save();

            $useNewId = $user->id;
            $notification = new Notification;
            $notification->noti_title = 'New Organizer: ' . $request['org_name'] . ' has been registered successfully.';
            $notification->noti_for = 'A';
            $notification->noti_forId = '1';
            $notification->noti_type = 'Reg';
            $notification->noti_typeId = $useNewId;
            $notification->noti_byId = $useNewId;
            $notification->save();

            return response()->json(['success' => 'Your Organization has been Register Successfully. Login now']);
        }
    }

    // Show forgot password form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? back()->with('status', __($response))
            : back()->withErrors(['email' => __($response)]);
    }

    // Show password reset form
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    // Reset password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    // Password broker
    public function broker()
    {
        return Password::broker();
    }


    public function logout()
    {
        session()->forget('user_id');
        session()->forget('user_name');
        session()->forget('org_name');
        session()->forget('user_email');
        session()->forget('user_type');
        if (Auth::guard('organizer')->check()) {
            Auth::guard('organizer')->logout();
        } elseif (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } elseif (Auth::guard('admin')->check()) {
            session()->forget('admin_id');
            session()->forget('admin_name');
            session()->forget('admin_email');
            Auth::guard('admin')->logout();
        }
        return redirect('login');
    }
}
