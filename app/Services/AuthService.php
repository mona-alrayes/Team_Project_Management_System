<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


/**
 * Class AuthService
 *
 * Handles operations related to users including login , register , logout , refresh.
 */
class AuthService
{

    public function login(array $data): array
    {
        try {
            // Attempt to authenticate with credentials
            $credentials = $data['email'] + $data['password'];
            $token = Auth::attempt($credentials);

            if (!$token) {
                return response()->json([
                    'status' => 'خطأ',
                    'message' => 'غير مخول',
                ], 401);
            }
            // Get authenticated user
            $user = Auth::user();
            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (Exception $e) {
            // Handle any exceptions that may occur
            return [
                'status' => 'خطأ',
                'message' => 'حدث خطأ اثناء عملية الدخول.',
                'errors' => $e->getMessage(),
            ];
        }
    }

    /**
     * Register a new user.
     *
     * @param array $data
     * The array containing user registration data including 'name', 'email', 'password', and 'role'.
     *
     * @return array
     * An array containing the user resource, a JWT token, or an error response.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(array $data): array
    {
        try {
            // Manually create the user without the password
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->save();

            // Generate a JWT token for the user
            $token = Auth::login(user: $user);

            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'خطأ',
                'message' => 'حدث خطأ اثناء عملية إنشاء الحساب',
                'errors' => $e->getMessage(),
            ];
        }
    }

}
