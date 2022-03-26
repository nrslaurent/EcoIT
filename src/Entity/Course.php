<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CourseRepository::class)
 */
class Course
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
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="coursesCreated")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createBy;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="coursesChosen")
     */
    private $chosenBy;

    /**
     * @ORM\OneToMany(targetEntity=Section::class, mappedBy="containedIn")
     */
    private $sectionsContained;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture;

    public function __construct()
    {
        $this->chosenBy = new ArrayCollection();
        $this->sectionsContained = new ArrayCollection();
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

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getCreateBy(): ?User
    {
        return $this->createBy;
    }

    public function setCreateBy(?User $createBy): self
    {
        $this->createBy = $createBy;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getChosenBy(): Collection
    {
        return $this->chosenBy;
    }

    public function addChosenBy(User $chosenBy): self
    {
        if (!$this->chosenBy->contains($chosenBy)) {
            $this->chosenBy[] = $chosenBy;
        }

        return $this;
    }

    public function removeChosenBy(User $chosenBy): self
    {
        $this->chosenBy->removeElement($chosenBy);

        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSectionsContained(): Collection
    {
        return $this->sectionsContained;
    }

    public function addSectionsContained(Section $sectionsContained): self
    {
        if (!$this->sectionsContained->contains($sectionsContained)) {
            $this->sectionsContained[] = $sectionsContained;
            $sectionsContained->setContainedIn($this);
        }

        return $this;
    }

    public function removeSectionsContained(Section $sectionsContained): self
    {
        if ($this->sectionsContained->removeElement($sectionsContained)) {
            // set the owning side to null (unless already changed)
            if ($sectionsContained->getContainedIn() === $this) {
                $sectionsContained->setContainedIn(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
