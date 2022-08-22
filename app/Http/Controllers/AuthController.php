<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAuthRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiResponseController
{
    public function index()
    {
        try {
            $users = User::orderby('name')->get();

            if ($users) {
                return $this->apiResponse(true, 'Users List', 'users', UserResource::collection(User::all()), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, 'No registered users.', null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
        }
    }

    public function register(UserAuthRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3
        ]);

        if ($user) {
            return $this->apiResponse(true, 'Register User Successfully!', 'user', new UserResource($user), JsonResponse::HTTP_CREATED);
        }
        return $this->apiResponse(false, 'Register User Unsuccessfully!', null, null,  JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function login(UserAuthRequest $request)
    {
        try {

            $users = User::where('email', $request->email)->first();
            if (!$users) {
                return $this->apiResponse(false, 'Invalid email.', null, null, JsonResponse::HTTP_NOT_FOUND);
            }
            if (!Hash::check($request->password, $users->password)) {
                return $this->apiResponse(false, 'Invalid password.', null, null, JsonResponse::HTTP_NOT_FOUND);
            }
            $token = $users->createToken('token')->plainTextToken;
            return $this->apiResponse(true, 'Login Successfully', 'token', $token, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function update(UserAuthRequest $request, $id)
    {

        $user = User::find($id);

        if (User::where('email', $request->email)->first() == null || $user->email == $request->email) {
            $user->name = $request->name ?? $user->name;
            $user->password = $request->password ?? $user->password;
            $user->email = $request->email ?? $user->email;
            $user->save();
            if ($user) {
                return $this->apiResponse(true, "User Update Successfully!", 'updated user', new UserResource($user), JsonResponse::HTTP_OK);
            } else {
                return $this->apiResponse(true, "User Update Unsuccessfully!", null, null, JsonResponse::HTTP_NOT_FOUND);
            }
        }
        return $this->apiResponse(true, "Book already exists.", null, null, JsonResponse::HTTP_NOT_FOUND);
    }

    public function roleAssignment(UserAuthRequest $request, $id)
    {
        $user = User::find($id);
        $user->role_id = $request->role_id;
        $result = $user->save();

        if ($result) {
            return $this->apiResponse(true, "User Role Assignment Successfully!", 'user', new UserResource($user), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, 'User Role Assignment Unsuccessfully!', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function editProfile(UserAuthRequest $request)
    {
        /*$this->middleware(function ($request, $next) {
            return $next($request);
        });*/

        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $user->update([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email' => $request->email,
        ]);

        if ($user) {
            return $this->apiResponse(true, "Profile Update Successfully!", 'user', new UserResource(User::find($user_id)), JsonResponse::HTTP_OK);
        } else {
            return $this->apiResponse(false, 'Profile Update Unsuccessfully!', null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function logout(Request $request)
    {
        try {
            $result = $request->user()->currentAccessToken()->delete();
            if ($result) {
                return $this->apiResponse(true, "Logout Successfully!", null, null, JsonResponse::HTTP_OK);
            } else {
                return $this->apiResponse(false, "Logout Unsuccessfully!", null, null, JsonResponse::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function limitedUserInfo()
    {
        try {
            $users = User::orderby('name')->get();
            if ($users) {
                return $this->apiResponse(true, "List of Usernames!", 'users', UserResource::collection($users)->pluck('name'), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, "No registered users.", null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function search($search)
    {
        try {
            $usersearch = User::where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc')->get();
            if ($usersearch) {
                return $this->apiResponse(true, "User Search", 'users', userResource::collection($usersearch)->pluck('name'), JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function getLibrarians()
    {
        try {
            $users = User::where('role_id', '=', '2')->get();
            if ($users) {
                return $this->apiResponse(true, "Librarians", 'librarian users', UserResource::collection($users)->pluck('name', 'id'), JsonResponse::HTTP_OK);
            }
            return $this->apiResponse(false, "No registered users.", null, null, JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->apiResponse(false, $e->getMessage(), null, null, JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
