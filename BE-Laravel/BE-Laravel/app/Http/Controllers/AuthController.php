<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AESHelper;
use App\Helpers\CryptoHelper;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        $secretKey = "MY_SUPER_SECRET_KEY_256";

        $emailPayload = $request->email;
        $passwordPayload = $request->password;

        // jika payload adalah AES ciphertext → decrypt
        if (is_string($emailPayload) && str_starts_with($emailPayload, "U2FsdGVkX1")) {
            $email = CryptoHelper::decryptAES($emailPayload, $secretKey);
        } else {
            $email = $emailPayload; // plaintext dari Postman
        }

        if (is_string($passwordPayload) && str_starts_with($passwordPayload, "U2FsdGVkX1")) {
            $password = CryptoHelper::decryptAES($passwordPayload, $secretKey);
        } else {
            $password = $passwordPayload; // plaintext dari Postman
        }

        if (!$email || !$password) {
            return response()->json([
                'message' => 'Gagal decrypt payload'
            ], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                "message" => "Format email tidak valid"
            ], 422);
        }

        $student = User::firstWhere("email", $email);

        if (!$student || !Hash::check($password, $student->password)) {
            return response()->json([
                "message" => "Bad credentials"
            ], Response::HTTP_NOT_FOUND);
        }

        $token = $student->createToken("sanctum_token")->plainTextToken;

        return response()->json([
            "message" => "Successfully logged in",
            "token" => $token
        ], Response::HTTP_OK);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message'       => 'Successfully logged out'
        ], Response::HTTP_OK);
    }

}
