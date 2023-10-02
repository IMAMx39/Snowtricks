<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\AtLeastOneOf([
        new Assert\Regex('/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube(-nocookie)?\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|live\/|v\/)?)([\w\-]+)(\S+)?$/'),
        new Assert\Regex('/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/'),
    ],
        message: 'The video URL is invalid, please insert the URL of a Youtube or Dailymotion video.',
        includeInternalMessages: false
    )]
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
        if ($videoUrl) {
            if (str_contains($videoUrl, 'youtube.com')) {
                if (str_contains($videoUrl, '/embed/')) {
                    $this->filename = $videoUrl;
                } else {
                    $this->filename = str_replace('https://www.youtube.com/watch?v=', 'https://www.youtube.com/embed/', $videoUrl);
                }
            }
            elseif (str_contains($videoUrl, 'dailymotion.com')) {
                if (str_contains($videoUrl, '/embed/')) {
                    $this->filename = $videoUrl;
                } else {
                    $this->filename = str_replace('https://www.dailymotion.com/video/', 'https://www.dailymotion.com/embed/video/', $videoUrl);
                }
            }
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
