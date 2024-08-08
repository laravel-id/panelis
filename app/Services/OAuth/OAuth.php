<?php

namespace App\Services\OAuth;

use Carbon\Carbon;

interface OAuth
{
    public function getProvider(): string;

    public function setAppKey(string $key): self;

    public function setAppSecret(string $secret): self;

    public function setAuthorizationCode(string $code): self;

    public function setRedirectUri(string $uri): self;

    public function setState(string $state): self;

    public function getAuthorizeUrl(): string;

    public function getToken(): ?string;

    public function getRefreshToken(): ?string;

    public function getExpiresIn(): ?Carbon;

    public function getError(): ?string;

    public function getUser(?string $token = null): self;

    public function getName(): ?string;

    public function authorize(?string $refreshToken = null): self;
}
