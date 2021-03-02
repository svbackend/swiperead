<?php

namespace App\Entity\User;

use App\Repository\UserRepository;
use App\ValueObject\User\UserId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    use UserInterfaceTrait;
    /**
     * @ORM\Id
     * @ORM\Column(type="user_id")
     */
    private UserId $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $lastName;

    /**
     * @ORM\Embedded(class="UserEmail", columnPrefix=false)
     */
    private UserEmail $email;

    /**
     * @psalm-var Collection<array-key,UserNetwork>
     * @ORM\OneToMany(targetEntity="UserNetwork", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $networks;

    /**
     * @psalm-var Collection<array-key,UserRole>
     * @ORM\OneToMany(targetEntity="UserRole", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $roles;

    public function __construct(
        UserId $id,
        string $firstName,
        string $lastName,
        UserEmail $email,
        string $network,
        string $networkId,
    )
    {
        $this->id = $id;

        Assert::notEmpty($firstName);
        Assert::notEmpty($lastName);

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;

        $this->roles = new ArrayCollection();
        $this->roles->add(new UserRole($this, UserRole::ROLE_USER));

        $this->networks = new ArrayCollection();
        $this->networks->add(new UserNetwork($this, $network, $networkId));
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    /** @psalm-return Collection<array-key,UserNetwork> */
    public function getNetworks(): Collection
    {
        return $this->networks;
    }

    /** @psalm-return Collection<array-key,UserRole> */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function getName(): string
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }
}
