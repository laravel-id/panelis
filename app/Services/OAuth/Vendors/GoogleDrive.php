<?php

namespace App\Services\OAuth\Vendors;

use App\Services\OAuth\OAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GoogleDrive implements OAuth
{
    private string $state;

    private array $user = [];

    private string $redirectUri;

    private string $authorizationCode;

    private string $clientId;

    private ?string $token = null;

    private ?string $refreshToken = null;

    private string $clientSecret;

    private ?string $error = null;

    private int $expiresIn = 0;

    public function getProvider(): string
    {
        return 'Google Drive';
    }

    public function setAppKey(string $key): OAuth
    {
        $this->clientId = $key;

        return $this;
    }

    public function setAppSecret(string $secret): OAuth
    {
        $this->clientSecret = $secret;

        return $this;
    }

    public function setAuthorizationCode(string $code): OAuth
    {
        $this->authorizationCode = $code;

        return $this;
    }

    public function setRedirectUri(string $uri): OAuth
    {
        $this->redirectUri = $uri;

        return $this;
    }

    public function setState(string $state): OAuth
    {
        $this->state = $state;

        return $this;
    }

    public function getAuthorizeUrl(): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $this->state,
            'response_type' => 'code',
            'scope' => 'https%3A//www.googleapis.com/auth/drive.metadata.readonly',
            'access_type' => 'offline',
            'include_granted_scopes' => 'true',
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?'.$params;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresIn(): ?Carbon
    {
        if (! empty($this->expiresIn)) {
            return Carbon::now()->addSeconds($this->expiresIn);
        }

        return null;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getUser(?string $token = null): OAuth
    {
        $response = Http::throwIf(app()->isLocal())->get('https://www.googleapis.com/drive/v3/about');

        $this->user = $response->json();

        return $this;
    }

    public function getName(): ?string
    {
        return data_get($this->user, 'user.displayName');
    }

    public function authorize(?string $refreshToken = null): OAuth
    {
        $response = Http::throwIf(app()->isLocal())
            ->asForm()
            ->post('https://oauth2.googleapis.com/token', [
                'code' => $this->authorizationCode,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'grant_type' => 'authorization_code',
            ]);

        if ($response->successful()) {
            $this->token = $response->json('access_token');
            $this->expiresIn = $response->json('expires_in');
            $this->refreshToken = $response->json('refresh_token');
        }

        return $this;
    }
}
