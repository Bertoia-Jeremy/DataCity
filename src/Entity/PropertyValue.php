<?php

namespace App\Entity;

use App\Repository\PropertyValueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropertyValueRepository::class)
 */
class PropertyValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $mutation_id;

    /**
     * @ORM\Column(type="date")
     */
    private $mutation_date;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city_name;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $sale_type;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $parcelle_id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $building_type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $surface_building;

    /**
     * @ORM\Column(type="integer")
     */
    private $surface_field;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $piece_number;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $latitude;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $number_lot;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="propertyValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $code_nature_culture;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nature_culture;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $code_nature_culture_speciale;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nature_culture_speciale;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMutationId(): ?string
    {
        return $this->mutation_id;
    }

    public function setMutationId(string $mutation_id): self
    {
        $this->mutation_id = $mutation_id;

        return $this;
    }

    public function getMutationDate(): ?\DateTimeInterface
    {
        return $this->mutation_date;
    }

    public function setMutationDate(\DateTimeInterface $mutation_date): self
    {
        $this->mutation_date = $mutation_date;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getCityName(): ?string
    {
        return $this->city_name;
    }

    public function setCityName(string $city_name): self
    {
        $this->city_name = $city_name;

        return $this;
    }

    public function getSaleType(): ?string
    {
        return $this->sale_type;
    }

    public function setSaleType(string $sale_type): self
    {
        $this->sale_type = $sale_type;

        return $this;
    }

    public function getParcelleId(): ?string
    {
        return $this->parcelle_id;
    }

    public function setParcelleId(?string $parcelle_id): self
    {
        $this->parcelle_id = $parcelle_id;

        return $this;
    }

    public function getBuildingType(): ?string
    {
        return $this->building_type;
    }

    public function setBuildingType(string $building_type): self
    {
        $this->building_type = $building_type;

        return $this;
    }

    public function getSurfaceBuilding(): ?int
    {
        return $this->surface_building;
    }

    public function setSurfaceBuilding(int $surface_building): self
    {
        $this->surface_building = $surface_building;

        return $this;
    }

    public function getSurfaceField(): ?int
    {
        return $this->surface_field;
    }

    public function setSurfaceField(int $surface_field): self
    {
        $this->surface_field = $surface_field;

        return $this;
    }

    public function getPieceNumber(): ?int
    {
        return $this->piece_number;
    }

    public function setPieceNumber(?int $piece_number): self
    {
        $this->piece_number = $piece_number;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getNumberLot(): ?int
    {
        return $this->number_lot;
    }

    public function setNumberLot(?int $number_lot): self
    {
        $this->number_lot = $number_lot;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCodeNatureCulture(): ?string
    {
        return $this->code_nature_culture;
    }

    public function setCodeNatureCulture(?string $code_nature_culture): self
    {
        $this->code_nature_culture = $code_nature_culture;

        return $this;
    }

    public function getNatureCulture(): ?string
    {
        return $this->nature_culture;
    }

    public function setNatureCulture(?string $nature_culture): self
    {
        $this->nature_culture = $nature_culture;

        return $this;
    }

    public function getCodeNatureCultureSpeciale(): ?string
    {
        return $this->code_nature_culture_speciale;
    }

    public function setCodeNatureCultureSpeciale(?string $code_nature_culture_speciale): self
    {
        $this->code_nature_culture_speciale = $code_nature_culture_speciale;

        return $this;
    }

    public function getNatureCultureSpeciale(): ?string
    {
        return $this->nature_culture_speciale;
    }

    public function setNatureCultureSpeciale(?string $nature_culture_speciale): self
    {
        $this->nature_culture_speciale = $nature_culture_speciale;

        return $this;
    }
}
