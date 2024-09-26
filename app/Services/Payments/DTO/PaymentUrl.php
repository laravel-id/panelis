<?php

namespace App\Services\Payments\DTO;

class PaymentUrl
{
    private string $id = '';

    private string $vendor;

    private array $customers = [];

    private array $items = [];

    private string $bank_id;

    private ?string $description = null;

    private ?string $note = null;

    private string $redirectUrl;

    private int|float $subTotal = 0;

    private int|float $total = 0;

    private string $paymentUrl;

    public function setCustomer(
        string $name,
        ?string $phone = null,
        ?string $email = null,
    ): self {
        $this->customers = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ];

        return $this;
    }

    public function getCustomer(): array
    {
        return $this->customers;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setVendor(string $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setBankId(string $bank_id): self
    {
        $this->bank_id = $bank_id;

        return $this;
    }

    public function getBankId(): string
    {
        return $this->bank_id;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setRedirectUrl(string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function setTotal(float|int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getTotal(): int|float
    {
        return $this->total;
    }

    public function setPaymentUrl(string $url): self
    {
        $this->paymentUrl = $url;

        return $this;
    }

    public function getPaymentUrl(): string
    {
        return $this->paymentUrl;
    }
}
