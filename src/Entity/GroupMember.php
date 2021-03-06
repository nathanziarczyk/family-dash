<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GroupMemberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "post"
 *      },
 *     itemOperations={
 *     "get",
 *     "delete"
 *      },
 * )
 * @ORM\Entity(repositoryClass=GroupMemberRepository::class)
 */
class GroupMember
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="groupMembers")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Groups({"group:item:read", "group:read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="groupMembers")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $groep;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accepted = false;

    public function __toString()
    {
        $groupName= $this->getGroep()->getName();
        $userEmail = $this->getUser()->getEmail();
        return "$groupName: $userEmail";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getGroep(): ?Group
    {
        return $this->groep;
    }

    public function setGroep(?Group $groep): self
    {
        $this->groep = $groep;

        return $this;
    }

    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }
}
