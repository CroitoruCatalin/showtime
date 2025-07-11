<?php

namespace App\Entity;

use App\Repository\FestivalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: FestivalRepository::class)]
#[Callback('validateDates')]
class Festival
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTime $start_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTime $end_date = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $price = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(
        targetEntity: Booking::class,
        mappedBy: 'festival',
        orphanRemoval: true,
        cascade: ['remove'],
    )]
    private Collection $bookings;

    /**
     * @var Collection<int, ScheduleSlot>
     */
    #[ORM\OneToMany(
        targetEntity: ScheduleSlot::class,
        mappedBy: 'festival',
        orphanRemoval: true,
        cascade: ['remove']
    )]
    private Collection $scheduleSlots;

    #[ORM\ManyToOne(targetEntity: Image::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Image $image = null;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->scheduleSlots = new ArrayCollection();
    }

    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->start_date && $this->end_date && $this->start_date > $this->end_date) {
            $context->buildViolation('Start date must be before end date.')
                ->atPath('start_date')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTime $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTime $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setFestival($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getFestival() === $this) {
                $booking->setFestival(null);
            }
        }

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
            $slot->setFestival($this);
        }

        return $this;
    }

    public function removeScheduleSlot(ScheduleSlot $slot): static
    {
        if ($this->scheduleSlots->removeElement($slot)) {
            // set the owning side to null (unless already changed)
            if ($slot->getFestival() === $this) {
                $slot->setFestival(null);
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
