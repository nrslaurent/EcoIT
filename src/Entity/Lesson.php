<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonRepository::class)
 */
class Lesson
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $video;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $resources = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $finishedBy = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="lessonsCreated")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="lessonsLearned")
     */
    private $learnedBy;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="lessonsContained")
     * @ORM\JoinColumn(nullable=false)
     */
    private $containedIn;

    public function __construct()
    {
        $this->learnedBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getResources(): ?array
    {
        return $this->resources;
    }

    public function setResources(?array $resources): self
    {
        $this->resources = $resources;

        return $this;
    }

    public function getFinishedBy(): ?array
    {
        return $this->finishedBy;
    }

    public function setFinishedBy(?array $finishedBy): self
    {
        $this->finishedBy = $finishedBy;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLearnedBy(): Collection
    {
        return $this->learnedBy;
    }

    public function addLearnedBy(User $learnedBy): self
    {
        if (!$this->learnedBy->contains($learnedBy)) {
            $this->learnedBy[] = $learnedBy;
        }

        return $this;
    }

    public function removeLearnedBy(User $learnedBy): self
    {
        $this->learnedBy->removeElement($learnedBy);

        return $this;
    }

    public function getContainedIn(): ?Section
    {
        return $this->containedIn;
    }

    public function setContainedIn(?Section $containedIn): self
    {
        $this->containedIn = $containedIn;

        return $this;
    }
}
