<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\FeedRepository")
 * @Vich\Uploadable
 */
class Feed
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"feed-read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"feed-read","feed-write"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"feed-read","feed-write"})
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"feed-read","feed-write"})
     */
    private $image;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"feed-read","feed-write"})
     */
    private $source;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"feed-read"})
     */
    private $created;

    /**
     * @ORM\ManyToOne (targetEntity="App\Entity\Publisher", inversedBy="feeds")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"feed-read"})
     */
    private $publisher;

    /**
     * @Vich\UploadableField(mapping="feedImage", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $updated;


    public function __construct()
    {
        $this->created = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(?\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getPublisher(): ?Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(?Publisher $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile = null): self
    {
        $this->imageFile = $imageFile;
        if(!is_null($imageFile)){
            $this->updated = new \DateTime('now');
        }
        return $this;

    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }
}
