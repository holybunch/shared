<?php

namespace holybunch\shared\security;

class JWTModel
{
    private string $iss;
    private string $sub;
    private int $userId;
    private string $picture;

    public function __construct()
    {
    }

    public function getIss(): string
    {
        return $this->iss;
    }

    public function getSub(): string
    {
        return $this->sub;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setIss(string $iss): void
    {
        $this->iss = $iss;
    }

    public function setSub(string $sub): void
    {
        $this->sub = $sub;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }
}
