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

#[ORM\Entity(repositoryClass: VideosRepository::class)]
#[Vich\Uploadable]
class Adminforumpage
{
	use TimestampableEntity;
	
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;
	
	#[ORM\Column(length: 255)]
    private ?string $username = null;
	
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;
	
	#[ORM\Column(length: 255)]
    private ?string $slugpage = null;
	
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
	
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
	
	public function getSlugpage(): ?string
    {
        return $this->slugpage;
    }

    public function setSlugpage(string $slugpage): static
    {
        $this->slugpage = $slugpage;

        return $this;
    }
}
