<?php

declare(strict_types=1);

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="users_networks", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"network", "network_id"})
 * })
 */
class UserNetwork
{
    public const NETWORKS = [
        self::NETWORK_GOOGLE,
        self::NETWORK_FACEBOOK,
    ];
    public const NETWORK_GOOGLE = 'google';
    public const NETWORK_FACEBOOK = 'fb';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="networks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private User $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $network;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $networkId;

    public function __construct(User $user, string $network, string $networkId)
    {
        Assert::oneOf($network, self::NETWORKS);
        $this->user = $user;
        $this->network = $network;
        $this->networkId = $networkId;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getNetworkId(): string
    {
        return $this->networkId;
    }
}