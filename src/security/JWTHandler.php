<?php

namespace holybunch\shared\security;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use holybunch\shared\exceptions\SharedException;

class JWTHandler
{
    private const ALG = 'HS256';

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function write(JWTModel $model, string $key, int $exp = 1800): string
    {
        $tokenData = [
            "iss" => $model->getIss(),
            "sub" => $model->getSub(),
            "user_id" => $model->getUserId(),
            "picture" => $model->getPicture(),
            "exp" => (time() + $exp),
            "nbf" => time(),
            "iat" => time()
        ];
        return JWT::encode($tokenData, $key, self::ALG);
    }

    public static function read(string $jwt, string $key): object
    {
        try {
            return JWT::decode($jwt, new Key($key, self::ALG));
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }
}
