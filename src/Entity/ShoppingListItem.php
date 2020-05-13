<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ShoppingListItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "get",
 *     "post"
 *      },
 *     itemOperations={
 *     "get",
 *     "delete"
 *      },
 *     normalizationContext={"groups"={"shoppingListItem:read"}},
 *     denormalizationContext={"groups"={"shoppingListItem:write"}},
 * )
 * @ORM\Entity(repositoryClass=ShoppingListItemRepository::class)
 */
class ShoppingListItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"shoppingListItem:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"shoppingListItem:read", "shoppingListItem:write", "shoppingList:write"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"shoppingListItem:read"})
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity=ShoppingList::class, inversedBy="shoppingListItems")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"shoppingListItem:read", "shoppingListItem:write"})
     */
    private $shoppingList;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shoppingListItems")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"shoppingListItem:read", "shoppingListItem:write"})
     */
    private $user;

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

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): self
    {
        $this->shoppingList = $shoppingList;

        return $this;
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
}
