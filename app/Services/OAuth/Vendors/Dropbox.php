<?php

namespace App\Services\OAuth\Vendors;

use App\Services\OAuth\OAuth;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Dropbox implements OAuth
{
    private string $clientId;

    private string $redirectUri;

    private string $state;

    private ?string $token = null;

    private ?string $refreshToken = null;

    private string $authorizationCode;

    private string $clientSecret;

    private int $expiresIn = 0;

    private ?string $error = null;

    private array $users = [];

    public function getProvider(): string
    {
        return 'Dropbox';
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
            'token_access_type' => 'offline',
        ]);

        return 'https://www.dropbox.com/oauth2/authorize?'.$params;
    }

    public function authorize(?string $refreshToken = null): self
    {
        if (! empty($refreshToken)) {
            $response = $this->getTokenByRefreshToken($refreshToken);
        } else {
            $response = $this->getTokenByCode();
        }

        if ($response->successful()) {
            $this->token = $response->json('access_token');
            $this->refreshToken = $response->json('refresh_token');
            $this->expiresIn = $response->json('expires_in');

            return $this;
        }

        $this->error = $response->json('error_description');

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getExpiresIn(): ?Carbon
    {
        return Carbon::now()->addSeconds($this->expiresIn);
    }

    /**
     * @throws Exception
     */
    public function getUser(?string $token = null): OAuth
    {
        $token = null;
        if (! empty(config('dropbox.expired_at'))) {
            $expiredAt = CarbonImmutable::parse(config('dropbox.expired_at'));
            if (now()->lt($expiredAt)) {
                $token = config('filesystems.disks.dropbox.token');
            }
        }

        if (empty($token)) {
            $auth = $this->authorize(config('dropbox.refresh_token'));
            $token = $auth->getToken();
        }

        $response = Http::throwIf(app()->isLocal())
            ->withToken($token)
            ->send('POST', 'https://api.dropboxapi.com/2/users/get_current_account');

        if (! $response->successful()) {
            $this->error = $response->json('error_description');
        }

        $this->users = $response->json();

        return $this;
    }

    public function getName(): ?string
    {
        return data_get($this->users, 'name.display_name');
    }

    private function getTokenByRefreshToken(string $refreshToken): PromiseInterface|Response
    {
        return Http::asForm()
            ->throwIf(app()->isLocal())
            ->post('https://api.dropbox.com/oauth2/token', [
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId ?? config('dropbox.client_id'),
                'client_secret' => $this->clientSecret ?? config('dropbox.client_secret'),
            ]);
    }

    private function getTokenByCode(): PromiseInterface|Response
    {
        return Http::asForm()
            ->throwIf(app()->isLocal())
            ->post('https://api.dropbox.com/oauth2/token', [
                'code' => $this->authorizationCode,
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
            ]);
    }
}
