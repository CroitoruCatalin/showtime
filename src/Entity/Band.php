<?php

namespace App\Entity;

use App\Enum\MusicGenre;
use App\Repository\BandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BandRepository::class)]
class
Band
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(enumType: MusicGenre::class)]
    private ?MusicGenre $genre = null;

    /**
     * @var Collection<int, Festival>
     */
    #[ORM\ManyToMany(targetEntity: Festival::class, mappedBy: 'bands')]
    private Collection $festivals;

    public function __construct()
    {
        $this->festivals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $Name): static
    {
        $this->name = $Name;

        return $this;
    }

    public function getGenre(): ?MusicGenre
    {
        return $this->genre;
    }
    public function setGenre(MusicGenre $Genre): static
    {
        $this->genre = $Genre;
        return $this;
    }

    /**
     * @return Collection<int, Festival>
     */
    public function getFestivals(): Collection
    {
        return $this->festivals;
    }

    public function addFestival(Festival $festival): static
    {
        if (!$this->festivals->contains($festival)) {
            $this->festivals->add($festival);
            $festival->addBand($this);
        }

        return $this;
    }

    public function removeFestival(Festival $festival): static
    {
        if ($this->festivals->removeElement($festival)) {
            $festival->removeBand($this);
        }

        return $this;
    }
}
