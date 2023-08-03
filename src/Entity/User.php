<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
	
	#[ORM\Column(length: 180, unique: true)]
                                        private ?string $username = null;
	
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;
	
	#[Assert\File(
                                                     maxSize: '1000m',
                                                     extensions: [
                                             			'jpg',
                                             			'jpeg',
                                             			'png'
                                             		],
                                                     extensionsMessage: 'Please upload a valid image file.',
                                                 )]
                                             	#[Vich\UploadableField(mapping: 'pfp', fileNameProperty: 'pfpName')]
                                                 private ?File $pfp = null;
	
	#[ORM\Column(length: 255, nullable: true)]
                                                 private ?string $pfpName = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Videos::class)]
    private Collection $videos;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comments::class)]
    private Collection $comments;
	
	public function __construct()
                                                 {
                                                     $this->createdAt = new \DateTimeImmutable();
                                                     $this->videos = new ArrayCollection();
                                                     $this->comments = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
	
	/**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $pfp
     */
    public function setPfp(?File $pfp = null): void
    {
        $this->pfp = $pfp;

        if (null !== $pfp) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
			$this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPfp(): ?File
    {
        return $this->pfp;
    }
	
	public function getPfpName(): ?string
                                                 {
                                                     return $this->pfpName;
                                                 }

    public function setPfpName(?string $pfpName): void
    {
        $this->pfpName = $pfpName;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Videos>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Videos $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setUser($this);
        }

        return $this;
    }

    public function removeVideo(Videos $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getUser() === $this) {
                $video->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
}
