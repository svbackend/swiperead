<?php

declare(strict_types=1);

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users_roles", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"role", "user_id"})
 * })
 */
class UserRole
{
    public const ROLES = [
        self::ROLE_USER,
        self::ROLE_CEO,
    ];

    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_CEO = 'ROLE_CEO';


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $role;

    public function __construct(
        User $user,
        string $role
    )
    {
        Assert::oneOf($role, self::ROLES);
        $this->user = $user;
        $this->role = $role;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function __toString(): string
    {
        return $this->role;
    }
}