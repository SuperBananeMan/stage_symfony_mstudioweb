// <?php

// namespace App\Entity;

// use Symfony\Component\HttpFoundation\File\File;
// use Vich\UploaderBundle\Mapping\Annotation as Vich;
// use Gedmo\Mapping\Annotation\Slug;
// use Gedmo\Timestampable\Traits\TimestampableEntity;
// use App\Repository\UserRepository;
// use Doctrine\DBAL\Types\Types;
// use Doctrine\ORM\Mapping as ORM;
// use Symfony\Component\Validator\Constraints as Assert;

// #[ORM\Entity(repositoryClass: UserRepository::class)]
// #[Vich\Uploadable]
// class User
// {
    // #[ORM\Id]
    // #[ORM\GeneratedValue]
    // #[ORM\Column]
    // private ?int $id = null;

    // #[ORM\Column(length: 100)]
    // private ?string $username = null;

    // #[ORM\Column(length: 100)]
    // private ?string $email = null;

    // #[ORM\Column(length: 255)]
    // private ?string $password = null;
	
	// #[Assert\File(
        // maxSize: '1000m',
        // extensions: [
			// 'jpg',
			// 'jpeg',
			// 'png',
			// 'gif'
		// ],
        // extensionsMessage: 'Please upload a valid image or gif file.',
    // )]
	// #[Vich\UploadableField(mapping: 'pfp', fileNameProperty: 'pfpName')]
    // private ?File $pfp = null;
	
	// #[ORM\Column(length: 255, nullable: true)]
    // private ?string $pfpName = null;
	
	// public function __construct()
    // {
        // $this->createdAt = new \DateTimeImmutable();
    // }

    // public function getId(): ?int
    // {
        // return $this->id;
    // }

    // public function getUsername(): ?string
    // {
        // return $this->username;
    // }

    // public function setUsername(string $username): static
    // {
        // $this->username = $username;

        // return $this;
    // }

    // public function getEmail(): ?string
    // {
        // return $this->email;
    // }

    // public function setEmail(string $email): static
    // {
        // $this->email = $email;

        // return $this;
    // }

    // public function getPassword(): ?string
    // {
        // return $this->password;
    // }

    // public function setPassword(string $password): static
    // {
        // $this->password = $password;

        // return $this;
    // }
	
	// /**
     // * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     // * of 'UploadedFile' is injected into this setter to trigger the update. If this
     // * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     // * must be able to accept an instance of 'File' as the bundle will inject one here
     // * during Doctrine hydration.
     // *
     // * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $pfp
     // */
    // public function setPfp(?File $pfp = null): void
    // {
        // $this->pfp = $pfp;

        // if (null !== $pfp) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            // $this->updatedAt = new \DateTimeImmutable();
        // }
    // }

    // public function getPfp(): ?File
    // {
        // return $this->pfp;
    // }
	
	// public function setPfpName(?string $pfpName): void
    // {
        // $this->pfpName = $pfpName;
    // }

    // public function getPfpName(): ?string
    // {
        // return $this->pfpName;
    // }
// }
