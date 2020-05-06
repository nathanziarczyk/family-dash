<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "get",
 *     "post"
 *      },
 *     itemOperations={
 *     "get",
 *     "put",
 *     "delete"
 *      },
 * )
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=GroupMember::class, mappedBy="groep")
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getGroupMembers(): Collection
    {
        $members = array();
        foreach ($this->groupMembers as $member){
            if ($member->getAccepted() === true){
                $members[] = $member->getUser();
            }
        }
        return new ArrayCollection($members);
    }


//    public function addGroupMember(GroupMember $groupMember): self
//    {
//        if (!$this->groupMembers->contains($groupMember)) {
//            $this->groupMembers[] = $groupMember;
//            $groupMember->setGroep($this);
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
//            if ($groupMember->getGroep() === $this) {
//                $groupMember->setGroep(null);
//            }
//        }
//
//        return $this;
//    }

}
