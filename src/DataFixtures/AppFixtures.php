<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private string $dataImagesDir;
    private string $tricksPicsDir;


    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->dataImagesDir = dirname(__DIR__) . '/Data/Images/';
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // create 3 users! Bam!
        $users = [];
        for ($i = 0; $i < 3; ++$i) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setCreatedAt(new DateTimeImmutable());
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(true);
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;

        }

        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setEmail('admin@admin.com');
        $userAdmin->setCreatedAt(new DateTimeImmutable());
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setIsVerified(true);
        $userAdmin->setPassword($this->hasher->hashPassword($userAdmin, 'admin123'));
        $manager->persist($userAdmin);

        // create categories
        $categories = [];
        foreach (['Grab', 'Rotation désaxées', 'Slide', 'Flip', 'Old school'] as $value) {
            $category = new Category();
            $category->setName($value);
            $manager->persist($category);
            $categories[] = $category;
        }

        $tricks = [];
        $tricks[] = (new Trick())
            ->setTitle("Mute")
            ->setName("Mute")
            ->setCreatedAt(new DateTimeImmutable())
            ->setCategory($categories[0])
            ->setDescription("Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.\nUn grab est d'autant plus réussi que la saisie est longue. De plus, le saut est d'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d'accentuer la torsion de son corps grâce à la tension de sa main sur la planche.\nOn dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).");

        $tricks[] = (new Trick())
            ->setTitle("Style Week")
            ->setName("Style Week")
            ->setCreatedAt(new DateTimeImmutable())
            ->setCategory($categories[0])
            ->setDescription("Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.\nUn grab est d'autant plus réussi que la saisie est longue. De plus, le saut est d'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d'accentuer la torsion de son corps grâce à la tension de sa main sur la planche.\nOn dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).");

        $tricks[] = (new Trick())
            ->setTitle("Truck Driver")
            ->setName("Truck Driver")
            ->setCategory($categories[0])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture).\nUn grab est d'autant plus réussi que la saisie est longue. De plus, le saut est d'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d'accentuer la torsion de son corps grâce à la tension de sa main sur la planche.\nOn dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).");

        $tricks[] = (new Trick())
            ->setTitle("Stalefish")
            ->setName("Stalefish")
            ->setCategory($categories[0])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.\nUn grab est d'autant plus réussi que la saisie est longue. De plus, le saut est d'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d'accentuer la torsion de son corps grâce à la tension de sa main sur la planche.\nOn dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).");

        $tricks[] = (new Trick())
            ->setTitle("1080 ou Big Foot")
            ->setName("1080 ou Big Foot")
            ->setCategory($categories[1])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Trois tours en rotation horizontale.\nUne rotation peut être agrémentée d'un grab, ce qui rend le saut plus esthétique mais aussi plus difficile car la position tweakée a tendance à déséquilibrer le rideur et désaxer la rotation.");

        $tricks[] = (new Trick())
            ->setTitle("Front Flip")
            ->setName("Front Flip")
            ->setCategory($categories[2])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Rotation en avant dans les airs.\nIl est possible de faire plusieurs flips à la suite, et d'ajouter un grab à la rotation.");

        $tricks[] = (new Trick())
            ->setTitle("Back Flip")
            ->setName("Back Flip")
            ->setCategory($categories[2])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Rotation en arrière dans les airs.\nLes flips agrémentés d'une vrille existent aussi (Mac Twist, Hakon Flip...), mais de manière beaucoup plus rare, et se confondent souvent avec certaines rotations horizontales désaxées.");

        $tricks[] = (new Trick())
            ->setTitle("Slide")
            ->setName("Slide")
            ->setCategory($categories[3])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Un slide consiste à glisser sur une barre de slide.\nLe slide se fait soit avec la planche dans l'axe de la barre, soit perpendiculaire, soit plus ou moins désaxé.");

        $tricks[] = (new Trick())
            ->setTitle("Nose Slide")
            ->setName("Nose Slide")
            ->setCategory($categories[3])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Slide avec l'avant de la planche sur la barre.");

        $tricks[] = (new Trick())
            ->setTitle("Tail Slide")
            ->setName("Tail Slide")
            ->setCategory($categories[3])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Slide avec l'arrière de la planche sur la barre");
        // Placeholder Tricks

        for ($i = 0; $i < 15; $i++) {
            $tricks[] = (new Trick())
                ->setTitle("Trick démo-$i")
                ->setName("Trick démo-$i")
                ->setCategory($categories[2])
                ->setDescription("Pour les besoins de la démonstration")
                ->setCreatedAt(new DateTimeImmutable());
        }

        foreach ($tricks as $trick) {
            $manager->persist($trick);
        }

        $manager->flush();
    }
}
