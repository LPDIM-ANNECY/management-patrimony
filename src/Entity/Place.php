<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PlaceRepository::class)
 * @Vich\Uploadable()
 */
class Place
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFileName;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="places_image", fileNameProperty="imageFileName")
     */
    #[Assert\Image(
        maxSize: "2M",
        mimeTypes: ['image/jpeg'],
        notFoundMessage: "Le fichier n'a pas été trouvé sur le disque",
        mimeTypesMessage: "Le fichier choisi ne correspond pas à un fichier valide (JPEG)",
        uploadErrorMessage: "Erreur dans l'upload du fichier"
    )]
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    #[Assert\Length(
        min: 3,
        max: 255,
    )]
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="integer")
     */
    private $difficulty;

    /**
     * @ORM\Column(type="integer")
     */
    private $radius_type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="places")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity=Itinerary::class, mappedBy="places")
     */
    private $itineraries;

    public function __construct()
    {
        $this->itineraries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getRadiusType(): ?int
    {
        return $this->radius_type;
    }

    public function setRadiusType(int $radius_type): self
    {
        $this->radius_type = $radius_type;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?category
    {
        return $this->category;
    }

    public function setCategory(?category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Itinerary[]
     */
    public function getItineraries(): Collection
    {
        return $this->itineraries;
    }

    public function addItinerary(Itinerary $itinerary): self
    {
        if (!$this->itineraries->contains($itinerary)) {
            $this->itineraries[] = $itinerary;
            $itinerary->addPlace($this);
        }

        return $this;
    }

    public function removeItinerary(Itinerary $itinerary): self
    {
        if ($this->itineraries->removeElement($itinerary)) {
            $itinerary->removePlace($this);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    /**
     * @param string|null $imageFileName
     * @return Place
     */
    public function setImageFileName(?string $imageFileName): Place
    {
        $this->imageFileName = $imageFileName;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImage(): ?File
    {
        return $this->image;
    }

    /**
     * @param File|null $image
     * @return Place
     */
    public function setImage(?File $image): Place
    {
        $this->image = $image;
        return $this;
    }

}
