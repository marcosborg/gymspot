<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DeleteAccountNotification;
use App\Models\Client;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',

        ], [], [
            'name' => 'Nome',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Confirmação da password',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->roles()->attach(2);
        
        $client = new Client;
        $client->name = $user->name;
        $client->user_id = $user->id;
        $client->save();

        return $user;
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $credentials = request(['email', 'password']);
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Os dados fornecidos estão inválidos.',
                'errors' => [
                    'password' => [
                        'Credenciais inválidas'
                    ],
                ]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $access_token = $user->createToken('auth-token')->plainTextToken;

        return [
            'access_token' => $access_token,
        ];
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        Notification::route('mail', 'pm@gymspot.pt')
            ->notify(new DeleteAccountNotification($user));
    }

    public function saveToken(Request $request)
    {
        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'Token salvo com sucesso.']);
    }
}
