<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "get",
 *     "post"
 * },
 *     itemOperations={
 *     "get",
 *     "put",
 *     "delete"
 *      },
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}},
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "user:write"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:read"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:write"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read", "user:write"})
     */
    private $isEnabled = false;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     */
    private $regKey;

    /**
     * @ORM\OneToMany(targetEntity=GroupMember::class, mappedBy="user")
     */
    private $groupMembers;

    public function __construct()
    {
        $this->groupMembers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getRegKey(): ?string
    {
        return $this->regKey;
    }

    public function setRegKey(string $string): self
    {
        if ($string === 'renew'){
            $this->regKey = md5(mt_rand());
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     * @Groups({"user:read"})
     */
    public function getGroups(): Collection
    {
        $groups = array();
        foreach($this->groupMembers as $group){
            $groups[] = $group->getGroep();
        }
        return new ArrayCollection($groups);
    }

    /**
     * @return Collection|GroupMember[]
     * @Groups({"user:read"})
     */
    public function getInvitations(): Collection
    {
        $invitations = array();
        if ($this->groupMembers->isEmpty()) return new ArrayCollection();
        foreach($this->groupMembers as $group){
            if ($group->getAccepted() === false){
                $invitations[] = $group;
            }
        }
        return new ArrayCollection($invitations);
    }

//    public function addGroupMember(GroupMember $groupMember): self
//    {
//        if (!$this->groupMembers->contains($groupMember)) {
//            $this->groupMembers[] = $groupMember;
//            $groupMember->setUser($this);
//        }
//
//        return $this;
//    }
//
//    public function removeGroupMember(GroupMember $groupMember): self
//    {
//        if ($this->groupMembers->contains($groupMember)) {
//            $this->groupMembers->removeElement($groupMember);
//            // set the owning side to null (unless already changed)
//            if ($groupMember->getUser() === $this) {
//                $groupMember->setUser(null);
//            }
//        }
//
//        return $this;
//    }
}
