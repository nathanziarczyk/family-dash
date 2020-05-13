<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Helper\ShortenHtmlTrait;
use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
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
 *     "put",
 *     "delete"
 *      },
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
     * @Groups({"group:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"group:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Groups({"group:read"})
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"group:read"})
     */
    private $shortBody;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Groups({"group:read"})
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $groep;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
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

        return $this;
    }

    public function getShortBody(): ?string
    {
        return $this->shortBody;
    }

    public function setShortBody(): self
    {
        if(strlen($this->getBody()) <= 50){
            $this->shortBody = $this->getBody();
        } else {
            // TODO checken of dit werkt
            $this->shortBody = $this->truncate($this->getBody(), 50);
        }

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
