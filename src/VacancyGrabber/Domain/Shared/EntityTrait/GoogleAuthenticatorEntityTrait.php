<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\EntityTrait;

trait GoogleAuthenticatorEntityTrait
{
    private bool $googleAuthenticatorEnabled = false;

    private ?string $googleAuthenticatorToken = null;

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return $this->googleAuthenticatorEnabled;
    }

    public function setGoogleAuthenticatorEnabled(bool $googleAuthenticatorEnabled): static
    {
        $this->googleAuthenticatorEnabled = $googleAuthenticatorEnabled;

        return $this;
    }

    public function getGoogleAuthenticatorToken(): ?string
    {
        return $this->googleAuthenticatorToken;
    }

    public function setGoogleAuthenticatorToken(?string $googleAuthenticatorToken): static
    {
        $this->googleAuthenticatorToken = $googleAuthenticatorToken;

        return $this;
    }
}
