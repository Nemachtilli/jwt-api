<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthRequest;
use Dingo\Api\Routing\Helpers;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group Autenticación
 */
class AuthController extends Controller
{
    use Helpers;

     /**
     * Iniciar sesión
     *
     * <aside class="notice">¡Vamos! Inicia Sesión 😀</aside>
     *
     * @unauthenticated
     *
     * @bodyParam email string required Un correo electrónico válido y que exista en base de datos
     * @bodyParam password string required Una contraseña vinculada a ese correo
     *
     * @response
     * status=200
     * scenario="autenticado"
     * {
     *     "access_token": 1209121291028,
     *     "token_type": "bearer",
     *     "expires_in_minutes": 60
     * }
     *
     * @response
     *  status=401
     *  scenario="error de autenticación"
     *  {
     *      "message": "Credenciales incorrectas",
     *      "status_code": 401,
     * }
     *
     * @response
     * status=422
     * scenario="fallo en la validación"
     * {
     *     "status": "Error",
     *     "message": "Validation Error",
     *     "data": {
     *         "email": [
     *             "The email must be a valid email address."
     *         ],
     *         "password": [
     *             "The password is required."
     *         ]
     *     }
     * }
     *
     * @param AuthRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function login(AuthRequest $request): \Dingo\Api\Http\Response {
        $credentials = request(["email", "password"]);

        if (! $token = JWTAuth::attempt($credentials)) {
            $this->response->errorUnauthorized(
                "Credenciales Incorrectas",
            );
        }

        return $this->respondWithToken($token);
    }

    /**
     * Cierra sesión expirando el token
     *
     * <aside class="notice">¿Seguro que te quieres ir? 😕</aside>
     *
     * @authenticated
     *
     * @response status=204 scenario="sesión cerrada"
     *
     * @response status=401 scenario="token vacío o mal formateado"
     * {
     *     "message": "Wrong number of segments",
     *     "status_code": 401
     * }
     *
     * @response status=401 scenario="token bien formateado pero incorrecto"
     * {
     *     "message": "Could not decode token: Error while decoding to JSON: Control character error, possibly incorrectly encoded",
     *     "status_code": 401
     * }
     *
     * @response status=401 scenario="no se puede verificar el token"
     * {
     *     "message": "Token Signature could not be verified",
     *     "status_code": 401
     * }
     *
     * @return \Dingo\Api\Http\Response
     */
    public function logout(): \Dingo\Api\Http\Response {
        auth()->logout();
        $token = request()->header("Authorization");
        JWTAuth::parseToken()->invalidate($token);
        return $this->response()->noContent();
    }


    protected function respondWithToken($token): \Dingo\Api\Http\Response {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in_minutes' => auth('api')->factory()->getTTL()
        ]);
    }

}
