<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\verwalten;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function extractDomainFromEmail($email)
    {
        $parts = explode('@', $email);

        if (count($parts) === 2) {
            return $parts[0];
        } else {
            return false;
        }
    }

    public function createUser(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
            ]);
//dd($this->extractDomainFromEmail($request['email']) . 123465);
            $user = User::create([
                'name' => $validatedData['name'],
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($this->extractDomainFromEmail($request['email']) . 12345),
            ]);

            verwalten::create([
                'user_id' => $user->id,
                'stufe' => 0,
                'punkte' => 0,
            ]);

            return response()->json(['message' => 'User created successfully', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function loginUser(Request $request): Response
    {
        $input = $request->all();

        Auth::attempt($input);

        $user = Auth::user();

        $token = $user->createToken('example')->accessToken;
        return Response(['status' => 200,'token' => $token],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function getUserDetail(): Response
    {
        if(Auth::guard('api')->check()){
            $user = Auth::guard('api')->user();
            return Response(['data' => $user],200);
        }
        return Response(['data' => 'Unauthorized'],401);
    }

    /**
     * Display the specified resource.
     */
    public function userLogout(): Response
    {
        if(Auth::guard('api')->check()){
            $accessToken = Auth::guard('api')->user()->token();

                \DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $accessToken->id)
                    ->update(['revoked' => true]);
            $accessToken->revoke();

            return Response(['data' => 'Unauthorized','message' => 'User logout successfully.'],200);
        }
        return Response(['data' => 'Unauthorized'],401);
    }

    public function usersList()
    {
        $current_user = auth('api')->user();
        $users = User::where('id', '!=', $current_user->id)->get();
        $verwaltens = verwalten::whereIn('user_id', $users->pluck('id'))->get();
        $data = $users->map(function ($user) use ($verwaltens) {
            $verwalten = $verwaltens->where('user_id', $user->id)->first();

            if ($verwalten) {
                $punkte_procent = ($verwalten->punkte / 50000) * 100;
                $user->verwalten = $verwalten;
                $user->punkte_procent = $punkte_procent;
            } else {
                $user->verwalten = null;
                $user->punkte_procent = null;
            }
            return $user;
        });

        return response()->json(['data' => $data]);
    }


}
