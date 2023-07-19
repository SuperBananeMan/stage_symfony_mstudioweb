<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation\Slug;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Repository\VideosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideosRepository::class)]
class Videos
{
	use TimestampableEntity;
	
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;
	
	#[ORM\Column(length: 255)]
    private ?string $nom = null;
	
    #[ORM\Column(length: 255)]
    private ?string $title = null;
	
	#[ORM\Column(type: Types::TEXT, nullable:true)]
    private ?string $description = null;
	
	#[ORM\Column(length: 255)]
    private ?string $genre = null;
	
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $duree = null;
	
	#[ORM\Column(length: 150, unique: true)]
	#[Slug(fields: ['title'])]
    private ?string $slug = null;
	
	public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
	
    public function getId(): ?int
    {
        return $this->id;
    }
	
	public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
	
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
	
	public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
	
	public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(?\DateTimeInterface $duree): static
    {
        $this->duree = $duree;

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
