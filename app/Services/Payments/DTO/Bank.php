<?php

namespace App\Services\Payments\DTO;

class Bank
{
    private string $id;

    private string $code;

    private string $label;

    private string $username;

    private string $password;

    private string $accountName;

    private string $accountNumber;

    private float $balance = 0;

    private bool $isActive = true;

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setAccountName(string $accountName): self
    {
        $this->accountName = $accountName;

        return $this;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
