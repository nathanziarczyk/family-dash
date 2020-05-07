<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
 *     normalizationContext={"groups"={"group:read"}},
 *     denormalizationContext={"groups"={"group:write"}},
 * )
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 * @ApiFilter(PropertyFilter::class)
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"group:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"group:read", "group:write"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=GroupMember::class, mappedBy="groep")
     */
    private $groupMembers;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="groep")
     */
    private $events;


    public function __construct()
    {
        $this->groupMembers = new ArrayCollection();
        $this->events = new ArrayCollection();
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
     * @Groups({"group:read"})
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


/**
 * @return Collection|Event[]
 * @Groups({"group:read"})
 */
public function getEvents(): Collection
{
    return $this->events;
}

public function addEvent(Event $event): self
{
    if (!$this->events->contains($event)) {
        $this->events[] = $event;
        $event->setGroep($this);
    }

    return $this;
}

public function removeEvent(Event $event): self
{
    if ($this->events->contains($event)) {
        $this->events->removeElement($event);
        // set the owning side to null (unless already changed)
        if ($event->getGroep() === $this) {
            $event->setGroep(null);
        }
    }

    return $this;
}
}
