<?php

namespace Nienfba\Framework;

use LogicException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Nienfba\Framework\Entity;
use UnexpectedValueException;

/**
 * Utilise les classes JWT  : composer require firebase/php-jwt 
 */

class TokenJwt {

    private $payload;

    private $token;

    public function __construct(Entity $user)
    {
        $issuedAt = new \DateTimeImmutable();

        $this->payload = [
            'iat'  => $issuedAt->getTimestamp(),                        // Issued at: time when the token was generated
            'iss'  => JWT_SERVER_NAME,                                  // Issuer
            'nbf'  => $issuedAt->getTimestamp(),                        // Not before
            'exp'  => $issuedAt->modify('+6 minutes')->getTimestamp(),  // Expire
            'userName' => $user->getId()                                // User name
        ];

        $this->token = JWT::encode(
            $this->payload,
            JWT_SECRET_KEY,
            JWT_ALGO
        );
    }

    public function get() {
        return $this->token;
    }

    public function decode()
    {
        try {
            if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches))
                throw new UnexpectedValueException('Token not found');

            $this->token = $matches[1];
            JWT::decode($this->token, new Key(JWT_SECRET_KEY, JWT_ALGO));
        } catch (LogicException $e) {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        } catch (UnexpectedValueException $e) {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }
    }

}