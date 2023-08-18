<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private Filesystem $filesystem;
    private SluggerInterface $slugger;
    private UserPasswordHasherInterface $hasher;
    private string $tricksImagesDir;
    private string $fixturesPicsDir;

    public function __construct(
        Filesystem $filesystem,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $hasher,
        string $tricksImagesDir
    )
    {
        $this->filesystem = $filesystem;
        $this->slugger = $slugger;
        $this->hasher = $hasher;
        $this->tricksImagesDir = $tricksImagesDir;
        $this->fixturesPicsDir = dirname(__FILE__) . '/Data/Images/';
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $this->purgeDirectories();

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
            ->setSlug("Mute")
            ->setName("Mute")
            ->setCreatedAt(new DateTimeImmutable())
            ->setCategory($categories[0])
            ->setDescription("Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.\nUn grab est d'autant plus réussi que la saisie est longue. De plus, le saut est d'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d'accentuer la torsion de son corps grâce à la tension de sa main sur la planche.\nOn dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).");

        $tricks[] = (new Trick())
            ->setSlug("Style Week")
            ->setName("Style Week")
            ->setCreatedAt(new DateTimeImmutable())
            ->setCategory($categories[0])
            ->setDescription("Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.\nUn grab est d'autant plus réussi que la saisie est longue. De plus, le saut est d'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d'accentuer la torsion de son corps grâce à la tension de sa main sur la planche.\nOn dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).");

        $tricks[] = (new Trick())
            ->setSlug("Truck Driver")
            ->setName("Truck Driver")
            ->setCategory($categories[0])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture).\nUn grab est d'autant plus réussi que la saisie est longue. De plus, le saut est d'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d'accentuer la torsion de son corps grâce à la tension de sa main sur la planche.\nOn dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).");

        $tricks[] = (new Trick())
            ->setSlug("Stalefish")
            ->setName("Stalefish")
            ->setCategory($categories[0])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.\nUn grab est d'autant plus réussi que la saisie est longue. De plus, le saut est d'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d'accentuer la torsion de son corps grâce à la tension de sa main sur la planche.\nOn dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).");

        $tricks[] = (new Trick())
            ->setSlug("1080 ou Big Foot")
            ->setName("1080 ou Big Foot")
            ->setCategory($categories[1])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Trois tours en rotation horizontale.\nUne rotation peut être agrémentée d'un grab, ce qui rend le saut plus esthétique mais aussi plus difficile car la position tweakée a tendance à déséquilibrer le rideur et désaxer la rotation.");

        $tricks[] = (new Trick())
            ->setSlug("Front Flip")
            ->setName("Front Flip")
            ->setCategory($categories[2])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Rotation en avant dans les airs.\nIl est possible de faire plusieurs flips à la suite, et d'ajouter un grab à la rotation.");

        $tricks[] = (new Trick())
            ->setSlug("Back Flip")
            ->setName("Back Flip")
            ->setCategory($categories[2])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Rotation en arrière dans les airs.\nLes flips agrémentés d'une vrille existent aussi (Mac Twist, Hakon Flip...), mais de manière beaucoup plus rare, et se confondent souvent avec certaines rotations horizontales désaxées.");

        $tricks[] = (new Trick())
            ->setSlug("Slide")
            ->setName("Slide")
            ->setCategory($categories[3])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Un slide consiste à glisser sur une barre de slide.\nLe slide se fait soit avec la planche dans l'axe de la barre, soit perpendiculaire, soit plus ou moins désaxé.");

        $tricks[] = (new Trick())
            ->setSlug("Nose Slide")
            ->setName("Nose Slide")
            ->setCategory($categories[3])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Slide avec l'avant de la planche sur la barre.");

        $tricks[] = (new Trick())
            ->setSlug("Tail Slide")
            ->setName("Tail Slide")
            ->setCategory($categories[3])
            ->setCreatedAt(new DateTimeImmutable())
            ->setDescription("Slide avec l'arrière de la planche sur la barre");
        // Placeholder Tricks

        for ($i = 0; $i < 15; $i++) {
            $tricks[] = (new Trick())
                ->setSlug("Trick démo-$i")
                ->setName("Trick démo-$i")
                ->setCategory($categories[2])
                ->setDescription("Pour les besoins de la démonstration")
                ->setCreatedAt(new DateTimeImmutable());
        }
        // Load all Tricks medias
        $picFileNames = $this->loadPicturesFixtures();
        $videoUrls = $this->loadVideoUrls();

        foreach ($tricks as $trick)
        {
            if(empty($trick->getCreatedAt())) {
                $trick->setCreatedAt(new \DateTimeImmutable());
            }

            $trick->setSlug($this->slugger->slug($trick->getName()));

            // Set trick pictures
            $trickPics = $this->getRandomEntries($picFileNames, 4);
            foreach ($trickPics as $tPic) {
                $pFName = $this->picUpload($tPic, $trick->getSlug());
                $p = new Image();
                $p->setFileName($pFName);
                $p->setTrick($trick);
                $manager->persist($p);
            }

            // Set trick videos
            $trickVideos = $this->getRandomEntries($videoUrls, 3);
            foreach ($trickVideos as $vidUrl) {
                $v = new Video();
                $v->setFilename($vidUrl);
                $trick->addVideo($v);
            }
        }

        // Users
        $users = [];
        $johndoe = new User();
        $johndoe->setUsername("John Doe")
            ->setEmail("john.doe@gmail.com")
            ->setCreatedAt($this->getImmutableDateDaysAgo(mt_rand(0,14)))
            ->setPassword($this->hasher->hashPassword($johndoe, "Secret123"))
            ->setIsVerified(true);
        $users[] = $johndoe;

        $janedoe = new User();
        $janedoe->setUsername("Jane Doe")
            ->setEmail("jane.doe@gmail.com")
            ->setCreatedAt($this->getImmutableDateDaysAgo(mt_rand(0,14)))
            ->setPassword($this->hasher->hashPassword($johndoe, "Secret123"))
            ->setIsVerified(true);
        $users[] = $janedoe;

        // Comments from users on tricks
        $comments = $this->generateComments($users, $tricks);

        // ADMIN User
        $admin = new User();
        $admin->setUsername("Admin")
            ->setEmail("admin@p6.oc")
            ->setCreatedAt($this->getImmutableDateDaysAgo(mt_rand(0,14)))
            ->setPassword($this->hasher->hashPassword($johndoe, "Secret123"))
            ->setIsVerified(true)
            ->setRoles(['ROLE_ADMIN']);
        $users[] = $admin;


        // Persist entities
        foreach ($categories as $category) {
            $manager->persist($category);
        }

        foreach ($tricks as $trick) {
            $manager->persist($trick);
        }

        foreach ($users as $user) {
            $manager->persist($user);
        }

        foreach ($comments as $comment) {
            $manager->persist($comment);
        }

        $manager->flush();
    }

    private function loadPicturesFixtures() : array
    {
        $pics = array();

        foreach (scandir($this->fixturesPicsDir) as $pic) {
            if ($pic !== '.' && $pic !== '..') {
                $pics[] = $pic;
            }
        }

        return $pics;
    }

    private function getRandomEntries(array $sourceArray, int $count = 5) : array
    {
        $results = array();
        for($i=0; $i < $count; $i++) {
            $results[] = $sourceArray[mt_rand(0, count($sourceArray) - 1)];
        }

        return $results;
    }

    private function picUpload(string $fileName, string $trickSlug) : string
    {
        $picFile = new File($this->fixturesPicsDir.$fileName);
        $targetDir = $this->tricksImagesDir . $trickSlug;

        if( !$this->filesystem->exists($targetDir) ) {
            $this->filesystem->mkdir($targetDir);
        }

        $safeFilename = $this->slugger->slug($fileName);
        $newFileName = $safeFilename.'-'.uniqid().'.'.$picFile->guessExtension();

        $this->filesystem->copy($picFile->getPathname(), $targetDir.'/'.$newFileName, true);
        return $newFileName;
    }

    private function loadVideoUrls() : array
    {
        $videos = array();
        $videosFilename = dirname(__FILE__) . '/Data/Videos.txt';

        $handle = fopen($videosFilename, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if(!empty($line)) {
                    $videos[] = trim($line);
                }
            }

            fclose($handle);
        }

        return $videos;
    }

    /**
     * @throws Exception
     */
    private function generateComments(array $users, array $tricks) : array
    {
        $comments = [];

        $lorems = [
            "In vitae tincidunt tellus. Cras congue viverra commodo.\nQuisque ultrices sapien enim, auctor lobortis dolor facilisis at.\n\nAenean dignissim, dolor sit amet mattis ultrices, dolor nunc fermentum ex, ut rhoncus magna dui vitae augue.",
            "Nulla laoreet neque libero, aliquam pretium libero vehicula nec.\nSed quis urna blandit, semper urna vel, aliquam sem.\n\nMauris euismod odio a pretium accumsan.",
            "Ut luctus ex ut placerat interdum.\n\nVivamus hendrerit massa et mauris pretium ultricies.\nConsequat vehicula lacus. Phasellus elementum dapibus nibh eu fermentum.",
            "Aenean volutpat bibendum nisl a interdum.\n\nVivamus sagittis dui a tellus tempor aliquet.",
            "Phasellus interdum nulla metus, ut porta dui pharetra eget.",
            "Donec sit amet mollis ipsum.\n\nDuis suscipit nisl sed scelerisque faucibus."
        ];

        foreach($tricks as $trick)
        {
            $maxCommentsCount = mt_rand(8, 15);
            for($i = 0; $i < $maxCommentsCount; $i++)
            {
                $user = $users[mt_rand(0, count($users) - 1)];
                $com = new Comment();
                $com->setTrick($trick);
                $com->setAuthor($user);
                $com->setContent($lorems[mt_rand(0, count($lorems) - 1)]);
                $com->setCreatedAt($this->getImmutableDateDaysAgo(mt_rand(0,14)));
                $comments[] = $com;
            }
        }

        return $comments;
    }

    /**
     * @throws Exception
     */
    private function getImmutableDateDaysAgo(int $days) : \DateTimeImmutable
    {
        $daysInterval = new \DateInterval("P{$days}D");
        $strDate = ((new \DateTime())->sub($daysInterval))->format(DateTimeInterface::ATOM);
        return new \DateTimeImmutable($strDate);
    }

    private function purgeDirectories(): void
    {
        // Remove, if any, pre-existing trick pictures directories
        foreach (scandir($this->tricksImagesDir) as $element) {
            if ($element !== '.' && $element !== '..') {
                $elFullpath = $this->tricksImagesDir.$element;
                if(is_dir($elFullpath)) {
                    $this->filesystem->remove($elFullpath);
                }
            }
        }
    }
}
