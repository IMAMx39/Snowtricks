<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    private ?Trick $trick = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $videoUrl): static
    {
        if (str_contains($videoUrl, '/embed/')) {
            $this->filename = $videoUrl;
            return $this;
        }
        if (str_contains($this->filename, 'https://www.youtube.com/watch?v=')) {
            $this->filename = str_replace('https://www.youtube.com/watch?v=', 'https://www.youtube.com/embed/', $videoUrl);
        }

        if (str_contains($videoUrl, 'www.dailymotion.com/')) {
            $this->filename = str_replace('www.dailymotion.com/', 'www.dailymotion.com/embed/', $videoUrl);
        }
        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): static
    {
        $this->trick = $trick;

        return $this;
    }
}
