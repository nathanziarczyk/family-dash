<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

// TODO PUT DELETE ADMIN
/**
 * @ApiResource(
 *     collectionOperations={
 *     "post"={
 *          "security_post_denormalize"="is_granted('ROLE_USER') and user.getGroups().contains(object.getGroep())"
 *      },
 *      },
 *     itemOperations={
 *     "get"={
 *          "security" = "is_granted('ROLE_USER') and user.getGroups().contains(object.getGroep())",
 *     },
 *     "put"={
 *          "denormalization_context"={"groups"={"event:item:put"}}
 *     },
 *     "delete"
 *      },
 *     normalizationContext={"groups"={"event:read"}},
 *     denormalizationContext={"groups"={"event:write"}},
 * )
 * @ApiFilter(PropertyFilter::class)
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"event:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event:read", "event:write", "event:item:put", "group:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event:read", "event:write", "event:item:put", "group:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"event:read", "event:write", "event:item:put", "group:read"})
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"event:read", "event:write", "event:item:put", "group:read"})
     */
    private $end;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"event:read", "event:write"})
     */
    private $groep;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="events")
     * @Groups({"event:read", "group:read", "user:read"})
     */
    private $attendants;

    public function __construct()
    {
        $this->attendants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

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

    /**
     * @return Collection|User[]
     */
    public function getAttendants(): Collection
    {
        return $this->attendants;
    }

    /**
     * Een deelnemer toevoegen aan een event
     * @Groups("event:item:put")
     */
    public function setNewAttendant(User $attendant): self
    {
        if (!$this->attendants->contains($attendant)) {
            $this->attendants[] = $attendant;
        }

        return $this;
    }
    /**
     * Een deelnemer verwijderen van een event
     * @Groups("event:item:put")
     */
    public function setRemoveAttendant(User $attendant): self
    {
        if ($this->attendants->contains($attendant)) {
            $this->attendants->removeElement($attendant);
        }

        return $this;
    }

}
