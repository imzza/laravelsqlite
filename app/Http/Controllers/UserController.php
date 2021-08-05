<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * User Profile Returns the user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request){
        return response()->json($request->user(), 200);
    }

    /**
     * Update user profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,'.$request->user()->id,
            'avatar' => 'dimensions:max_width=256,max_height=256',
            'name' => 'required',
            'user_name' => 'required|min:4|max:20',
            'c_password' => 'same:password',
        ]);

        if($validator->fails()){
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $user = User::where('id',$request->user()->id)->first();

        if($user){
            $user->name      = $request->name;
            $user->user_name = $request->user_name;
            $user->email     = $request->email;

            if($request->has("password")){
                $request->password = bcrypt($request->password);
                $user->password  = $request->password;
            }

            if($request->avatar){
                $filename =  time() . '.' . $request->avatar->getClientOriginalExtension();
                $request->avatar->move(public_path('users') . '/images/' , $filename);
                $user->avatar    = 'users/images/' . $filename;
            }

            if ($user->save()){
                return response()->json(["message" => "Your profile is updated!"], 200);
            }else{
                return response()->json(["message" => "Profile update failed!"], 400);
            }

        }else{
            return response()->json(["message" => "Unauthenticated."], 401);
        }
    }

    /**
     * Login API returns the Access token or error
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $response = Http::asForm()->post(url('/oauth/token'), [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '*',
        ]);
        return $response->json();
    }

    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $response = Http::asForm()->post(url('/oauth/token'), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
            'scope' => '*'
        ]);

        return $response->json();
    }




    /**
     * Logout the User and Revoke the User Token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(["message" => "User Logged out successfully"], 200);
    }

    /**
     * Register api registeres the user and send the activation PIN in Email
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_name' => 'required|min:4|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'avatar' => 'required|image|mimes:jpeg,png,jpg|dimensions:max_width=256,max_height=256',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $randomid = mt_rand(100000, 999999);
        $request->password = bcrypt($request->password);
        $filename = time() . '.' . $request->avatar->getClientOriginalExtension();
        $request->avatar->move(public_path('users') . '/images/', $filename);

        $user = new User;
        $user->name = $request->name;
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->avatar = 'users/images/' . $filename;
        $user->pin = $randomid;
        $user->save();

        $success['name'] = $user->name;
        $success['user_role'] = 'user';

        $email_send_to = $request->email;
        $mailData = array('pin' => $randomid);

        $subject = 'Verify Account';

        Mail::send('emails.verification', $mailData, function ($message) use ($email_send_to) {
            $message->to($email_send_to)->subject('Verification');
        });
        return response()->json(["message" => "An account confirmation PIN is sent to your Email."], 201);
    }

    public function send_invitation_link(Request $request)
    {
        $user_role = $request->user()->user_role;
        if ($user_role == 'admin') {
            $validator = Validator::make($request->all(), [
                'email_send_to' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }
            $email_send_to = $request->email_send_to;
            $details = [
                'title' => 'Verification',
                'heading' => 'Please click on the link to register your account.',
                'text' => 'Register',
            ];
            $subject = 'Invitation';
            Mail::send('emails.invitation', $details, function ($message) use ($email_send_to) {
                $message->to($email_send_to)->subject('Invitation');
            });
            return response()->json(["message" => "Invitation link sent successfully."], 200);
        } else {
            return response()->json(["message" => "Unauthenticated."], 401);
        }
    }

    /**
     * Activate user account with PIN
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users',
            'pin' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->is_active == 1) {
            return response()->json(["message" => "Your account is already active."], 200);
        }
        if (isset($user->pin) && $request->pin == $user->pin) {
            $user->is_active = 1;
            $user->pin = null;
            $user->save();
            return response()->json(["message" => "Your account has been activated! Please login."], 200);
        } else {
            return response()->json(["message" => "The PIN you entered is invalid."], 400);
        }
    }

}
