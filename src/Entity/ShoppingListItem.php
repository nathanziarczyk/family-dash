<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ShoppingListItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "post"={
 *          "security_post_denormalize"="is_granted('ROLE_USER') and user.getGroups().contains(object.getShoppingList().getGroep())"
 *      },
 *      },
 *     itemOperations={
 *     "get"={
 *          "security" = "is_granted('ROLE_USER') and user.getGroups().contains(object.getShoppingList().getGroep())",
 *     },
 *     "delete"={
 *              "security" = "is_granted('ROLE_USER') and object.getUser() == user",
 *          }
 *      },
 *     normalizationContext={"groups"={"shoppingListItem:read"}},
 *     denormalizationContext={"groups"={"shoppingListItem:write"}},
 * )
 * @ORM\Entity(repositoryClass=ShoppingListItemRepository::class)
 * @ORM\EntityListeners({"App\Doctrine\ShoppingListItemEntityListener"})
 */
class ShoppingListItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"shoppingListItem:read", "shoppingList:read", "group:item:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"shoppingListItem:read", "shoppingListItem:write", "shoppingList:write", "shoppingList:read", "group:item:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"shoppingListItem:read", "shoppingList:read", "group:item:read"})
     * @Gedmo\Timestampable(on="create")
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
     * @Groups({"shoppingListItem:read", "shoppingListItem:write", "shoppingList:read", "group:item:read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=ShoppingCategories::class, inversedBy="shoppingListItems")
     * @Groups({"shoppingListItem:read", "shoppingListItem:write", "shoppingList:read", "group:item:read" })
     */
    private $category;

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

    public function getCategory(): ?ShoppingCategories
    {
        return $this->category;
    }

    public function setCategory(?ShoppingCategories $category): self
    {
        $this->category = $category;

        return $this;
    }
}
