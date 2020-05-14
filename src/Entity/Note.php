<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Helper\ShortenHtmlTrait;
use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={
 *     "get",
 *     "post"
 * },
 *     itemOperations={
 *     "get",
 *     "put" = {
 *     "denormalization_context"={"groups"={"note:item:put"}}
 *     },
 *     "delete"
 *      },
 *     normalizationContext={"groups"={"note:read"}},
 *     denormalizationContext={"groups"={"note:write"}},
 * )
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    use ShortenHtmlTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"group:read", "note:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"group:read", "note:read", "note:write", "note:item:put"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Groups({"group:read", "note:read", "note:write", "note:item:put"})
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"group:read", "note:read"})
     */
    private $shortBody;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"group:read", "note:read"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Groups({"note:read", "note:write"})
     */
    private $groep;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Groups({"note:read", "note:write"})
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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        if(strlen($body) > 50){
            $this->shortBody = $this->truncate($body, 50, '...');
        } else {
            $this->shortBody = $body;
        }
        return $this;
    }

    public function getShortBody(): ?string
    {
        return $this->shortBody;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
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
