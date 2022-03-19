<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $skills;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValidated;

    /**
     * @ORM\OneToMany(targetEntity=Course::class, mappedBy="createBy")
     */
    private $coursesCreated;

    /**
     * @ORM\ManyToMany(targetEntity=Course::class, mappedBy="chosenBy")
     */
    private $coursesChosen;

    /**
     * @ORM\OneToMany(targetEntity=Section::class, mappedBy="createdBy")
     */
    private $sectionsCreated;

    /**
     * @ORM\OneToMany(targetEntity=Lesson::class, mappedBy="createdBy")
     */
    private $lessonsCreated;

    /**
     * @ORM\ManyToMany(targetEntity=Lesson::class, mappedBy="learnedBy")
     */
    private $lessonsLearned;

    public function __construct()
    {
        $this->coursesCreated = new ArrayCollection();
        $this->coursesChosen = new ArrayCollection();
        $this->sectionsCreated = new ArrayCollection();
        $this->lessonsCreated = new ArrayCollection();
        $this->lessonsLearned = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(?string $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCoursesCreated(): Collection
    {
        return $this->coursesCreated;
    }

    public function addCoursesCreated(Course $coursesCreated): self
    {
        if (!$this->coursesCreated->contains($coursesCreated)) {
            $this->coursesCreated[] = $coursesCreated;
            $coursesCreated->setCreateBy($this);
        }

        return $this;
    }

    public function removeCoursesCreated(Course $coursesCreated): self
    {
        if ($this->coursesCreated->removeElement($coursesCreated)) {
            // set the owning side to null (unless already changed)
            if ($coursesCreated->getCreateBy() === $this) {
                $coursesCreated->setCreateBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCoursesChosen(): Collection
    {
        return $this->coursesChosen;
    }

    public function addCoursesChosen(Course $coursesChosen): self
    {
        if (!$this->coursesChosen->contains($coursesChosen)) {
            $this->coursesChosen[] = $coursesChosen;
            $coursesChosen->addChosenBy($this);
        }

        return $this;
    }

    public function removeCoursesChosen(Course $coursesChosen): self
    {
        if ($this->coursesChosen->removeElement($coursesChosen)) {
            $coursesChosen->removeChosenBy($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSectionsCreated(): Collection
    {
        return $this->sectionsCreated;
    }

    public function addSectionsCreated(Section $sectionsCreated): self
    {
        if (!$this->sectionsCreated->contains($sectionsCreated)) {
            $this->sectionsCreated[] = $sectionsCreated;
            $sectionsCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSectionsCreated(Section $sectionsCreated): self
    {
        if ($this->sectionsCreated->removeElement($sectionsCreated)) {
            // set the owning side to null (unless already changed)
            if ($sectionsCreated->getCreatedBy() === $this) {
                $sectionsCreated->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessonsCreated(): Collection
    {
        return $this->lessonsCreated;
    }

    public function addLessonsCreated(Lesson $lessonsCreated): self
    {
        if (!$this->lessonsCreated->contains($lessonsCreated)) {
            $this->lessonsCreated[] = $lessonsCreated;
            $lessonsCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removeLessonsCreated(Lesson $lessonsCreated): self
    {
        if ($this->lessonsCreated->removeElement($lessonsCreated)) {
            // set the owning side to null (unless already changed)
            if ($lessonsCreated->getCreatedBy() === $this) {
                $lessonsCreated->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessonsLearned(): Collection
    {
        return $this->lessonsLearned;
    }

    public function addLessonsLearned(Lesson $lessonsLearned): self
    {
        if (!$this->lessonsLearned->contains($lessonsLearned)) {
            $this->lessonsLearned[] = $lessonsLearned;
            $lessonsLearned->addLearnedBy($this);
        }

        return $this;
    }

    public function removeLessonsLearned(Lesson $lessonsLearned): self
    {
        if ($this->lessonsLearned->removeElement($lessonsLearned)) {
            $lessonsLearned->removeLearnedBy($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->email;
    }
}
