<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "get"
 *      },
 *     itemOperations={
 *     "get" = {
 *          "access_control" = "is_granted('ROLE_USER') and object == user",
 *     },
 *     "put" = {
 *          "access_control" = "is_granted('ROLE_USER') and object == user",
 *          "denormalization_context"={"groups"={"user:item:put"}}
 *     },
 *     "delete" = {
 *          "access_control" = "is_granted('ROLE_USER') and object == user",
 *     },
 *  },
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}},
 * )
 * @ApiFilter(SearchFilter::class, properties={"firstName": "exact", "lastName":"exact", "email":"partial"})
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
     * @Assert\NotBlank()
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
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write", "note:read"})
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank()
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

    /**
     * @ORM\ManyToMany(targetEntity=Event::class, mappedBy="attendants")
     */
    private $events;

    private $encoder;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="user")
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity=ShoppingListItem::class, mappedBy="user")
     */
    private $shoppingListItems;

    /**
     * @var ArrayCollection
     */
    private $invitations;

    public function __construct()
    {
        $this->groupMembers = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->shoppingListItems = new ArrayCollection();
        $this->invitations = new ArrayCollection();
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

    /** ************************************** */
    /***/                                   /***/
    /***/           /* Group */             /***/
    /***/                                   /***/
    /** ************************************** */

    /**
     * Geaccepteerde groepen ophalen
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        $groups = array();
        foreach($this->groupMembers as $group){
            if($group->getAccepted() === true){
                $groups[] = $group->getGroep();
            }
        }
        return new ArrayCollection($groups);
    }

    /**
     * Nog niet geaccepteerde uitnodigingen ophalen
     */
    public function getInvitations(): Collection
    {
        $invitations = [];
        if ($this->groupMembers->isEmpty()) return new ArrayCollection();
        foreach($this->groupMembers as $group){
            if ($group->getAccepted() === false){
                $invitations[] = $group;
            }
        }
        $this->invitations = new ArrayCollection($invitations);
        return $this->invitations;
    }

    /**
     * Uitnodiging accepteren
     * @Groups({"user:item:put"})
     * @param Group $group
     * @return User
     */
    public function setAcceptGroupRequest(Group $group): self
    {
        $invitations = $this->getInvitations();
        foreach ($invitations as $invitation){
            if($invitation->getGroep()->getId() === $group->getId()){
                $invitation->setAccepted(true);
                $this->invitations->removeElement($invitation);
            }
        }
        return $this;
    }

    /** ************************************** */
    /***/                                   /***/
    /***/           /* Events */            /***/
    /***/                                   /***/
    /** ************************************** */

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setNewAttendant($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            $event->removeAttendant($this);
        }

        return $this;
    }

    /** ************************************** */
    /***/                                   /***/
    /***/           /* Notes */             /***/
    /***/                                   /***/
    /** ************************************** */

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }

    /** ************************************** */
    /***/                                   /***/
    /***/           /* Shop */              /***/
    /***/                                   /***/
    /** ************************************** */

    /**
     * @return Collection|ShoppingListItem[]
     */
    public function getShoppingListItems(): Collection
    {
        return $this->shoppingListItems;
    }

    public function addShoppingListItem(ShoppingListItem $shoppingListItem): self
    {
        if (!$this->shoppingListItems->contains($shoppingListItem)) {
            $this->shoppingListItems[] = $shoppingListItem;
            $shoppingListItem->setUser($this);
        }

        return $this;
    }

    public function removeShoppingListItem(ShoppingListItem $shoppingListItem): self
    {
        if ($this->shoppingListItems->contains($shoppingListItem)) {
            $this->shoppingListItems->removeElement($shoppingListItem);
            // set the owning side to null (unless already changed)
            if ($shoppingListItem->getUser() === $this) {
                $shoppingListItem->setUser(null);
            }
        }

        return $this;
    }

}
