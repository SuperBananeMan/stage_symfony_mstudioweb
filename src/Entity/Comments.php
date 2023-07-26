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
class Comments
{
	use TimestampableEntity;
	
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;
	
	#[ORM\Column(length: 255)]
    private ?string $userName = null;
	
    #[ORM\Column(length: 255)]
    private ?string $content = null;
	
	#[ORM\Column(length: 255)]
    private ?int $uploader = null;
	
	#[ORM\Column(length: 255)]
    private ?int $video = null;
	
	public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
	
    public function getIdComment(): ?int
    {
        return $this->id;
    }
	
	public function getUserNameComment(): ?string
    {
        return $this->userName;
    }

    public function setUserNameComment(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }
	
    public function getContentComment(): ?string
    {
        return $this->content;
    }

    public function setContentComment(string $content): static
    {
        $this->content = $content;

        return $this;
    }
	
	public function setUploaderComment(?int $uploader): void
    {
        $this->uploader = $uploader;
    }

    public function getUploaderComment(): ?int
    {
        return $this->uploader;
    }
	
	public function setVideoComment(?int $video): void
    {
        $this->video = $video;
    }

    public function getVideoComment(): ?int
    {
        return $this->video;
    }
}
