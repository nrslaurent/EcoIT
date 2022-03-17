<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
 */
class Section
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sectionsCreated")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Course::class, inversedBy="sectionsContained")
     * @ORM\JoinColumn(nullable=false)
     */
    private $containedIn;

    /**
     * @ORM\OneToMany(targetEntity=Lesson::class, mappedBy="containedIn")
     */
    private $lessonsContained;

    public function __construct()
    {
        $this->lessonsContained = new ArrayCollection();
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getContainedIn(): ?Course
    {
        return $this->containedIn;
    }

    public function setContainedIn(?Course $containedIn): self
    {
        $this->containedIn = $containedIn;

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessonsContained(): Collection
    {
        return $this->lessonsContained;
    }

    public function addLessonsContained(Lesson $lessonsContained): self
    {
        if (!$this->lessonsContained->contains($lessonsContained)) {
            $this->lessonsContained[] = $lessonsContained;
            $lessonsContained->setContainedIn($this);
        }

        return $this;
    }

    public function removeLessonsContained(Lesson $lessonsContained): self
    {
        if ($this->lessonsContained->removeElement($lessonsContained)) {
            // set the owning side to null (unless already changed)
            if ($lessonsContained->getContainedIn() === $this) {
                $lessonsContained->setContainedIn(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}
