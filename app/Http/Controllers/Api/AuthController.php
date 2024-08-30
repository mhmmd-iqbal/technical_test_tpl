<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API Documentation",
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="admin2@kemenaker.go.id"),
     *             @OA\Property(property="password", type="string", format="password", example="11112222"),
     *             @OA\Property(property="role", type="string", example="admin", enum={"admin", "user"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="User has been registered successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=31536000),
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(property="refresh_token", type="string", example="def50200a738325ef221805b8b867c9342af...")
     *             )
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $role = $request->has('role') && $request->role === 'admin' ? 'admin' : 'user';

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $role,
        ]);

        $client = Client::where('password_client', 1)->first();

        $tokenRequest = $request->create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '*',
        ]);

        $response = app()->handle($tokenRequest);

        $data = json_decode($response->getContent());

        return response()->json([
            'success' => true,
            'statusCode' => 200,
            'message' => 'User has been registered successfully.',
            'data' => $data,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login an existing user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin2@kemenaker.go.id"),
     *             @OA\Property(property="password", type="string", format="password", example="11112222")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="User has been logged in successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=31536000),
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(property="refresh_token", type="string", example="def50200513b565cba304d304ee0496c8086...")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {    

            $client = Client::where('password_client', 1)->first();
    
            $tokenRequest = $request->create('/oauth/token', 'POST', [
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '*',
            ]);
    
            $response = app()->handle($tokenRequest);
    
            $data = json_decode($response->getContent());
    
            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'User has been logged in successfully.',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'statusCode' => 401,
                'message' => 'Unauthorized.',
                'errors' => 'Unauthorized',
            ], 401);
        }
    }
}
