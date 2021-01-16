<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends BaseUser
{
    const ROLE_USER = "ROLE_USER";
    const ROLE_ADMIN = "ROLE_ADMIN";


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $surnames;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $phone;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedDate;

    public function __construct()
    {
        parent::__construct();
    }

    public function __toString()
    {
        return $this->getName() . " " . $this->getSurnames() . " (" . $this->getId() . ")";
    }

    public function getId()
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurnames(): ?string
    {
        return $this->surnames;
    }

    public function setSurnames(?string $surnames): self
    {
        $this->surnames = $surnames;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function getCreatedDate(): ?\dateTime
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\dateTime $createdDate = null): self
    {
        $this->createdDate = $createdDate ? $createdDate : new \dateTime();
        return $this;
    }

    public function getUpdatedDate(): ?\dateTime
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(?\dateTime $updatedDate = null): self
    {
        $this->updatedDate = $updatedDate ? $updatedDate : new \dateTime();
        return $this;
    }


}