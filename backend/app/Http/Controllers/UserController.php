<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Aws\S3\S3Client;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $body = $request->all();
            $body['password'] = Hash::make($body['password']);
            $body['role'] = 'customer';
            $body['pic'] = 'images/nopic.png';
            $body['description'] = '';
            $user = User::create($body);
            return response($user, 201);
        } catch (\Exception $e) {
            return response(['message' => 'Hubo un problema para registrar el usuario', 'error' => $e->getMessage()], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            $usernameOrEmail = $request->input('usernameOrEmail');
            $password = $request->input('password');
            
            if (Str::contains($usernameOrEmail, '@')) {
                $credentials = ['email' => $usernameOrEmail, 'password' => $password];
            } else {
                $credentials = ['username' => $usernameOrEmail, 'password' => $password];
            }
            
            if (!Auth::attempt($credentials)) {
                return response(['message' => 'Wrong Credentials'], 400);
            }
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;
            return response(['user' => $user->load('followers', 'followings', 'posts'), 'token' => $token]);
        } catch (\Exception $e) {
            return response(['message' => 'Hubo un problema para iniciar sesión', 'error' => $e->getMessage()], 500);
        }
    }
    public function resetPassword(Request $request, $id) 
    {
        try {
            $body = $request->all();
            $newPassword = Hash::make($body['password']);
            $user = User::find($id);
            $user->update(['password' => $newPassword]);
            return response($user);
        } catch (\Exception $e) {
            return response(['error' => $e], 500);
        }
    }
    public function logout()
    {
        try {
            Auth::user()->token()->revoke();
            return response([
                'message'=>'User successfully disconected.'
            ]);
        } catch (\Exception $e) {
            return response([
                'message' => 'There was an error trying to login the user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAll()
    {
        try {
            return User::all();
        } catch (\Exception $e) {
            return response(['error' => $e], 500);
        }
    }
    public function getById($id)
    {
        try {
            $user = User::find($id);
            return response($user->load('followers', 'followings', 'posts'));
        } catch (\Exception $e) {
            return response([
                'error' => $e
            ], 500);
        }
    }
    public function getByUsername($username)
    {
        try {
            $user = User::where('username', $username)->first();
            return response($user->load('followers', 'followings', 'posts'));
        } catch (\Exception $e) {
            return response([
                'error' => $e
            ], 500);
        }
    }
    public function update(Request $request) {
        try {
            $request->validate([
                'name' => 'required|string',
                'username' => 'required|string',
                'email' => 'required|string']);
            $body = $request->all();    
            $user = Auth::user();
            $user->update($body);
            return response($user);
        } catch (\Exception $e) {
            return response([
                'error' => $e
            ], 500);
        }
    }
    public function uploadProfileImage(Request $request)
    {
        try {
            $request->validate(['image' => 'required|image']);
            $image_path = $request->image->store('images','s3');

            $user = Auth::user();
            
            $picDefault = "images/nopic.png";
            $oldPic = $user->pic;
            
            if ($oldPic !== $picDefault) {
                Storage::disk('s3')->delete($oldPic);
            }
            $user->update(['pic' => $image_path]);
            return response($user);
        } catch (\Exception $e) {
            dd($e);
            return response(['error' => $e,], 500);
        }
    }
    public function userInfo() {
        try {
            $user = Auth::user();
            return response($user->load('followers', 'followings', 'posts'));
        } catch (\Exception $e) {
            return response([
                'message' => 'There was an error trying to get the user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
