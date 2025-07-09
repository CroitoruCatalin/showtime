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
        ['name' => 'Fantan Mojah', 'genre' => MusicGenre::Reggae],
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
        $bandEntities = [];
        $festivalEntities = [];
        foreach ($this->bandsData as $data) {
            $band = new Band();
            $band->setName($data['name']);
            $band->setGenre($data['genre']);
            $manager->persist($band);
            $bandEntities[$data['name']] = $band;
        }

        foreach ($this->festivalsData as $data) {
            $festival = new Festival();
            $festival
                ->setName($data['name'])
                ->setStartDate(new \DateTime($data['start_date']))
                ->setEndDate(new \DateTime($data['end_date']))
                ->setLocation($data['location'])
                ->setPrice($data['price']);

            $manager->persist($festival);
            $festivalEntities[$data['name']] = $festival;
        }

        foreach ($this->scheduleSlotsData as $slotData) {
            $festival = $festivalEntities[$slotData['festivalName']] ?? null;
            $band = $bandEntities[$slotData['bandName']] ?? null;

            if (!$band || !$festival) {
                continue;
            }

            /** @var \DateTime $startTime */
            $startTime = new \DateTime($slotData['startTime']);
            /** @var \DateTime $endTime */
            $endTime = new \DateTime($slotData['endTime']);

            $slot = (new ScheduleSlot())
                ->setStartTime($startTime)
                ->setEndTime($endTime)
                ->setBand($band)
                ->setFestival($festival);

            $festival->addScheduleSlot($slot);
            $band->addScheduleSlot($slot);

            $manager->persist($slot);
        }


        foreach ($this->bookingsData as $data) {
            $booking = new Booking();
            $booking
                ->setFullName($data['full_name'])
                ->setEmail($data['email']);
            if (isset($festivalEntities[$data['festival']])) {
                $booking->setFestival($festivalEntities[$data['festival']]);
            }
            $manager->persist($booking);
        }

        $user = new User();
        $user->setEmail("admin@admin.com");
        $user->setPassword($this->hasher->hashPassword($user, "admin@admin.com"));
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setUsername("adminUser");
        $manager->persist($user);

        $manager->flush();
    }
}
