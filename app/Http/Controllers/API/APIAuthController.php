<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class APIAuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::whereEmail($data['email'])->first();

        if (!$user || !Hash::check($request->get('password'), $user->password)) {
            return $this->respondWithWrongCredentials();
        }

        return response()->json([
            'status' => 'success',
            'message' => "Successfully logged in user.",
            'data' => [
                'token' => $user->createToken('api', ['role:client'])->plainTextToken,
                'expires_at' => config('sanctum.expiration'),
                'user' => new UserResource($user)
            ]
        ]);
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        if($request->user() !== null) {
            // Revoke the token that was used to authenticate the current request...
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => "Successfully logged out user."
        ]);
    }

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|exists:App\Models\User,email',
            'password' => 'required',
        ]);

        if(User::whereEmail($data['email'])->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already taken. Try something else.'
            ], 403);
        }

        $user =  User::create([
            'first_name' =>  $data['first_name'],
            'last_name' =>  $data['last_name'],
            'email' =>  $data['email'],
            'password' =>  bcrypt($data['password']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Successfully registered user.",
            'data' => [
                'token' => $user->createToken('mobile', ['role:client'])->plainTextToken,
                'expires_at' => config('sanctum.expiration'),
                'user' => new UserResource($user)
            ]
        ]);
    }

    public function updateProfile(Request $request)//: \Illuminate\Http\JsonResponse
    {
        // $user =  $request->user('api');
        $user = User::first();

        $data = $request->validate([
            'first_name' => 'sometimes',
            'last_name' => 'sometimes',
            'email' => [
                'sometimes',
                // Rule::unique('clients')->ignore($user->id),
            ],
            'authors' => 'sometimes|array|nullable',
            'sources' => 'sometimes|array|nullable'
        ]);

        $user->first_name = e($data['first_name']);
        $user->last_name = e($data['last_name']);
        $user->email = e($data['email']);
        $user->authors = $data['authors'];
        $user->sources = $data['sources'];

        $user->save();

        $user->fresh();

        return response()->json([
            'status' => 'success',
            'message' => "Successfully updated user profile.",
            'data' => [
                'user' => new UserResource($user)
            ]
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithWrongCredentials(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'The provided credentials are incorrect.'
        ], 403);
    }

    public function authUserDetail(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return UserResource::collection([User::find($request->user('api')->id)]);
    }
}
