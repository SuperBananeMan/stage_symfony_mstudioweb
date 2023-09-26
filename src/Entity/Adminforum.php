<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Repository\VideosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdminforumRepository::class)]
#[Vich\Uploadable]
class Adminforum extends User
{
	use TimestampableEntity;
	
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;
	
	#[ORM\Column(length: 255)]
    private ?string $username = null;
	
    #[ORM\Column(length: 255)]
    private ?string $reason = null;
	
	#[ORM\Column(length: 255)]
    private ?string $status = null;
	
	#[ORM\Column(length: 255, unique: true)]
	#[Slug(fields: ['reason'])]
    private ?string $slug = null;
	
	public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
	
    public function getId(): ?int
    {
        return $this->id;
    }
	
	public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
	
    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }
	
	public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
	
	public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
