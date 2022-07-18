<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('user.validation')->only('update', 'editProfile');
        $this->middleware('role')->only('roleAssignment');
    }

    public function index()
    {
        try {
            $users = User::orderby('name')->get();

            if ($users) {
                return response()->json([
                    'status code' => 200,
                    'success' => true,
                    'user details' =>  UserResource::collection(User::all()),
                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered users.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 401,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function register(Request $request)
    {
        if (User::where('email', '=', $request->email)->first() == null) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 3
            ]);

            if ($user) {
                return response()->json([
                    'status code' => 201,
                    'success' => true,
                    'message' => "Register User Successfully",
                    'user' => new UserResource($user),
                ]);
            }

            return response()->json([
                'status code' => 401,
                'success' => false,
                'message' => 'Register User Unsuccessfully'
            ]);
        } else {
            return response()->json([
                'status code' => 401,
                'success' => false,
                'message' => 'Email already exists.'
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            $rules = [
                'password' => ['required', 'string'],
                'email' => ['required', 'string', 'email'],
            ];
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return response()->json([
                    'status code' => 401,
                    'success' => false,
                    'message' => $validation->errors()->all()
                ]);
            }
            $users = User::where('email', $request->email)->first();
            if (!$users) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email.',
                ]);
            }
            if (!Hash::check($request->password, $users->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password.',
                ]);
            }
            $token = $users->createToken('token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login Successfully',
                'token' => $token
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status code' => 400,
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email' => $request->email,
        ]);

        if ($user) {
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => "User Update Successfully",
                'updated user' => new UserResource(User::findOrFail($id))
            ]);
        } else {
            return response()->json([
                'status_code' => 401,
                'success' => false,
                'message' => "User Update Unsuccessfully",
            ]);
        }
    }

    public function roleAssignment(Request $request, $id)
    {
        $user = User::find($id);
        $user->role_id = $request->role_id;
        $result = $user->save();

        if ($result) {
            return response()->json([
                'status code' => 200,
                'success' => true,
                'message' => 'User Role Assignment Successfully'
            ]);
        } else {
            return response()->json([
                'status code' => 401,
                'success' => false,
                'message' => 'User Role Assignment Unsuccessfully'
            ]);
        }
    }

    public function editProfile(Request $request)
    {
        $this->middleware(function ($request, $next) {
            return $next($request);
        });

        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $user->update([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email' => $request->email,
        ]);

        if ($user) {
            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => "Profile Update Successfully",
                'updated user' => new UserResource(User::findOrFail($user_id))
            ]);
        } else {
            return response()->json([
                'status_code' => 401,
                'success' => false,
                'message' => "Profile Update Unsuccessfully",
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $result = $request->user()->currentAccessToken()->delete();
            if ($result) {
                return response()->json([
                    'status_code' => 200,
                    'success' => true,
                    'message' => "Logout Successfully",
                ]);
            } else {
                return response()->json([
                    'status_code' => 401,
                    'success' => false,
                    'message' => "Logout Unsuccessfully",
                ]);
            }
        } catch (\Exception $e) {

            return response()->json([
                'code' => 401,
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function limitedUserInfo()
    {
        try {
            $users = User::orderby('name')->get();
            if ($users) {
                return response()->json([
                    'status code' => 200,
                    'success' => true,
                    'user details' =>  UserResource::collection($users)->pluck('name'),
                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered users.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 401,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function search($search)
    {
        try {
            $usersearch = User::where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc')->get();
            if ($usersearch) {
                return response()->json([
                    'success' => true,
                    'user' =>  userResource::collection($usersearch)->pluck('name')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getLibrarians()
    {
        try {
            $users = User::where('role_id','=','2')->get();
            if ($users) {
                return response()->json([
                    'status code' => 200,
                    'success' => true,
                    'librarian names' =>  UserResource::collection($users)->pluck('name','id'),
                ]);
            }
            return response()->json([
                'status code' => 204,
                'success' => true,
                'message' => 'No registered users.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 401,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
