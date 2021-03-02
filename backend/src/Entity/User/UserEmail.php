<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class UserEmail
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isConfirmed;

    public function __construct(
        string $email,
        bool $isConfirmed,
    )
    {
        Assert::email($email);
        $this->email = $email;
        $this->isConfirmed = $isConfirmed;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
