<?php

namespace byteShard\Internal\Authentication;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
    private ?object $jwt;

    public function __construct(string $token, private readonly string $certPath)
    {
        $jwks = json_decode(file_get_contents($this->certPath), true);
        list($headBase64, $bodyBase64, $cryptoBase64) = explode('.', $token);
        $header = FirebaseJWT::jsonDecode(FirebaseJWT::urlsafeB64Decode($headBase64));
        $key    = [];
        foreach ($jwks['keys'] as $k) {
            if (isset($k['kid']) && $k['kid'] == $header->kid) {
                $key = $k;
                break;
            }
        }
        $cert = '';
        if (array_key_exists('x5c', $key) && count($key['x5c']) === 1) {
            $cert = $this->x5cToPem($key['x5c'][0]);
        }
        if ($cert !== '') {
            try {
                $this->jwt = FirebaseJWT::decode($token, new Key($cert, 'RS256'));
            } catch (ExpiredException $e) {
                $this->jwt = $e->getPayload();
            }
        }
    }

    public function getPreferredUsername(): string
    {
        return $this->jwt->preferred_username;
    }

    public function getRealmAccessRoles(): array
    {
        if (isset($this->jwt->realm_access->roles) && is_array($this->jwt->realm_access->roles)) {
            return $this->jwt->realm_access->roles;
        }
        return [];
    }

    public function isTokenValid(): bool
    {
        if ($this->jwt === null) {
            return false;
        }
        if ($this->isTokenExpired() === true) {
            return false;
        }
        return true;
    }

    public function isTokenExpired(): bool
    {
        return time() > $this->jwt->exp;
    }

    public function getJwt(): ?object
    {
        return $this->jwt;
    }

    private function x5cToPem(string $x5c): string
    {
        $cert = "-----BEGIN CERTIFICATE-----\n";
        $cert .= chunk_split($x5c, 64, "\n");
        $cert .= "-----END CERTIFICATE-----\n";
        return $cert;
    }

    public static function refresh()
    {
        $url = "https://your-keycloak-server/auth/realms/{your-realm}/protocol/openid-connect/token";

        $data = array(
            'client_id'     => 'your-client-id',
            'client_secret' => 'your-client-secret',  // Required for confidential clients
            'grant_type'    => 'refresh_token',
            'refresh_token' => 'your-refresh-token',
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === FALSE) {
            // Handle error
        }

        $responseData = json_decode($response, true);
    }
}