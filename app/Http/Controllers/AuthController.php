<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Registrar un nuevo usuario",
     *     description="Permite registrar un nuevo usuario",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registro exitoso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="ID del usuario"),
     *                 @OA\Property(property="name", type="string", description="Nombre del usuario"),
     *                 @OA\Property(property="email", type="string", format="email", description="Correo del usuario")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error en el registro",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="El correo ya está en uso")
     *         )
     *     )
     * )
    */
    public function register(RegisterUserRequest $request)
    {
        $response = $this->authService->register($request);
        if (isset($response['error'])) {
            return response()->json($response, 401);
        }
        return response()->json($response);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autenticar un usuario",
     *     description="Permite autenticar a un usuario y obtener un token de acceso",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autenticación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token_type", type="string", description="Tipo de token"),
     *             @OA\Property(property="expires_in", type="integer", description="Tiempo de expiración del token"),
     *             @OA\Property(property="access_token", type="string", description="Token de acceso"),
     *             @OA\Property(property="refresh_token", type="string", description="Token de refresco")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error de autenticación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Login failed.")
     *         )
     *     )
     * )
     */
    public function login(LoginUserRequest $request)
    {
        $response = $this->authService->login($request);
        if (isset($response['error'])) {
            return response()->json($response, 401);
        }
        return response()->json($response);
    }
}
