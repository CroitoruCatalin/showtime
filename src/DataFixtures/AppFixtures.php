<?php

namespace App\DataFixtures;

use App\Entity\Band;
use App\Entity\Booking;
use App\Entity\Festival;
use App\Entity\ScheduleSlot;
use App\Entity\User;
use App\Enum\MusicGenre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private array $bandsData = [
        ['name' => 'AC/DC', 'genre' => MusicGenre::Rock],
        ['name' => 'Fantan Mojah', 'genre' => MusicGenre::Reggae],
        ['name' => 'Cinderella', 'genre' => MusicGenre::Rock],
        ['name' => 'Natural Black', 'genre' => MusicGenre::Reggae],
        ['name' => 'Junior Kelly', 'genre' => MusicGenre::Reggae],
        ['name' => 'Scorpions', 'genre' => MusicGenre::Rock],
        ['name' => 'Art of Trance', 'genre' => MusicGenre::Trance],
        ['name' => 'Trooper', 'genre' => MusicGenre::Metal],
        ['name' => 'Iris', 'genre' => MusicGenre::Rock],
        ['name' => 'Pheonix', 'genre' => MusicGenre::Rock],
        ['name' => 'Byron', 'genre' => MusicGenre::Rock],
        ['name' => 'Billy Joel', 'genre' => MusicGenre::Pop],
        ['name' => 'Time Bandits', 'genre' => MusicGenre::Pop],
        ['name' => 'Fragma', 'genre' => MusicGenre::House],
        ['name' => 'Jazzbit', 'genre' => MusicGenre::House],
        ['name' => 'Capella', 'genre' => MusicGenre::Electronic],
        ['name' => 'Scooter', 'genre' => MusicGenre::Electronic],
        ['name' => 'Guns \'n Roses', 'genre' => MusicGenre::Rock],
    ];
    private array $festivalsData = [
        [
            'name' => 'Untold 2025',
            'start_date' => '2025-07-15',
            'end_date' => '2025-07-22',
            'location' => 'Craiovita Noua',
            'price' => '125',
            'bands' => ['AC/DC', 'Cinderella', 'Scorpions'],
        ],
        [
            'name' => 'Neversea 2025',
            'start_date' => '2025-07-18',
            'end_date' => '2025-07-27',
            'location' => 'Craiovita Veche',
            'price' => '322',
            'bands' => ['Fantan Mojah', 'Natural Black', 'Junior Kelly'],
        ],
        [
            'name' => 'Intencity 2025',
            'start_date' => '2025-08-09',
            'end_date' => '2025-08-17',
            'location' => 'Brazda',
            'price' => '120.00',
            'bands' => ['Fragma', 'Jazzbit', 'Capella'],
        ],
    ];

    private array $scheduleSlotsData = [
        [
            'bandName' => 'AC/DC',
            'festivalName' => 'Untold 2025',
            'startTime' => '2025-07-15 17:00:00',
            'endTime' => '2025-07-15 22:00:00',
        ],
        [
            'bandName' => 'Trooper',
            'festivalName' => 'Untold 2025',
            'startTime' => '2025-07-15 15:00:00',
            'endTime' => '2025-07-15 16:45:00',
        ],
        [
            'bandName' => 'Trooper',
            'festivalName' => 'Untold 2025',
            'startTime' => '2025-07-16 15:00:00',
            'endTime' => '2025-07-16 17:00:00',
        ],
        [
            'bandName' => 'Guns \'n Roses',
            'festivalName' => 'Untold 2025',
            'startTime' => '2025-07-16 17:00:00',
            'endTime' => '2025-07-16 22:00:00',
        ],
    ];
    private array $bookingsData = [
        [
            'full_name' => 'Catalin Croitoru',
            'email' => 'catalin.croitoru@gmail.com',
            'festival' => 'Untold 2025',
        ],
        [
            'full_name' => 'Catalin Croitoru',
            'email' => 'catalin.croitoru@gmail.com',
            'festival' => 'Intencity 2025',
        ],
        [
            'full_name' => 'Popescu Nenumitu',
            'email' => 'popescu.nen@yahoo.com',
            'festival' => 'Untold 2025',
        ],
        [
            'full_name' => 'Denis Ciugud',
            'email' => 'denis.gud@gmail.com',
            'festival' => 'Neversea 2025',
        ],
        [
            'full_name' => 'Denis Ciugud',
            'email' => 'denis.gud@gmail.com',
            'festival' => 'Intencity 2025',
        ],
    ];

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $bandEntities = $this->createBands($this->bandsData, $manager);
        $festivalEntities = $this->createFestivals($this->festivalsData, $manager);
        $scheduleSlotEntities = $this->createSlots(
            $this->scheduleSlotsData,
            $festivalEntities,
            $bandEntities,
            $manager);

        $bookingEntities = $this->createBookings($this->bookingsData, $festivalEntities, $manager);

        $user = $this->createUser(
            'admin@admin.com',
            'admin@admin.com',
            ['ROLE_ADMIN'],
            'adminUser',
            $manager
        );
        $manager->flush();
    }

    private function createBands(
        array         $bandsData,
        ObjectManager $manager,
        bool          $flush = false
    ): array
    {
        $entities = [];
        foreach ($bandsData as $data) {
            $band = (new Band())
                ->setName($data['name'])
                ->setGenre($data['genre']);
            $manager->persist($band);
            $entities[$data['name']] = $band;
        }
        if ($flush) {
            $manager->flush();
        }
        return $entities;
    }
    
    private function createFestivals(
        array         $festivalsData,
        ObjectManager $manager,
        bool          $flush = false
    ): array
    {
        $entities = [];
        foreach ($festivalsData as $data) {
            $festival = (new Festival())
                ->setName($data['name'])
                ->setStartDate(new \DateTime($data['start_date']))
                ->setEndDate(new \DateTime($data['end_date']))
                ->setLocation($data['location'])
                ->setPrice($data['price']);
            $manager->persist($festival);
            $entities[$data['name']] = $festival;
        }
        if ($flush) {
            $manager->flush();
        }
        return $entities;
    }

    private function createSlots(
        array         $slotEntitiesData,
        array         $festivalEntities,
        array         $bandEntities,
        ObjectManager $manager,
        bool          $flush = false
    ): array
    {
        $scheduleSlots = [];

        foreach ($slotEntitiesData as $slotData) {
            $festName = $slotData['festivalName'];
            $bandName = $slotData['bandName'];
            $festival = $festivalEntities[$festName] ?? null;
            $band = $bandEntities[$bandName] ?? null;

            if (!isset($bandEntities[$bandName]) || !isset($festivalEntities[$festName])) {
                throw new \InvalidArgumentException("Invalid slot: band or festival not found");
            }

            $startTime = new \DateTime($slotData['startTime']);
            $endTime = new \DateTime($slotData['endTime']);

            $slot = (new ScheduleSlot())
                ->setStartTime($startTime)
                ->setEndTime($endTime)
                ->setBand($band)
                ->setFestival($festival);

            $festival->addScheduleSlot($slot);
            $band->addScheduleSlot($slot);
            $manager->persist($slot);
            $scheduleSlots[] = $slot;
        }
        if ($flush) {
            $manager->flush();
        }
        return $scheduleSlots;
    }


    private function createBookings(
        array         $bookingsData,
        array         $festivalEntities,
        ObjectManager $manager,
        bool          $flush = false
    ): array
    {
        $bookings = [];
        foreach ($bookingsData as $data) {
            $booking = new Booking();
            $booking
                ->setFullName($data['full_name'])
                ->setEmail($data['email']);
            if (isset($festivalEntities[$data['festival']])) {
                $booking->setFestival($festivalEntities[$data['festival']]);
            }
            $manager->persist($booking);
            $bookings[] = $booking;
        }
        if ($flush) {
            $manager->flush();
        }
        return $bookings;
    }


    private function createUser(
        string        $email,
        string        $password,
        array         $roles,
        string        $username,
        ObjectManager $manager,
        bool          $flush = false
    ): User
    {
        $user = (new User())
            ->setEmail($email)
            ->setUsername($username)
            ->setRoles($roles);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $manager->persist($user);
        if ($flush) {
            $manager->flush();
        }
        return $user;
    }
}
