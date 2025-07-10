<?php

namespace App\Entity;

use App\Enum\MusicGenre;
use App\Repository\BandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BandRepository::class)]
class   Band
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $name = null;

    #[ORM\Column(enumType: MusicGenre::class)]
    private ?MusicGenre $genre = null;

    /**
     * @var Collection<int, ScheduleSlot>
     */
    #[ORM\OneToMany(targetEntity: ScheduleSlot::class, mappedBy: 'band')]
    private Collection $scheduleSlots;

    #[ORM\ManyToOne(targetEntity: Image::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Image $image = null;

    public function __construct()
    {
        $this->scheduleSlots = new ArrayCollection();
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
     * @return Collection<int, ScheduleSlot>
     */
    public function getScheduleSlots(): Collection
    {
        return $this->scheduleSlots;
    }

    public function addScheduleSlot(ScheduleSlot $slot): static
    {
        if (!$this->scheduleSlots->contains($slot)) {
            $this->scheduleSlots->add($slot);
            $slot->setBand($this);
        }

        return $this;
    }

    public function removeScheduleSlot(ScheduleSlot $slot): static
    {
        if ($this->scheduleSlots->removeElement($slot)) {
            // set the owning side to null (unless already changed)
            if ($slot->getBand() === $this) {
                $slot->setBand(null);
            }
        }

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }
}
