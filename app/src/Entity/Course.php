<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'courses', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Institution $institution = null;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Intake::class, orphanRemoval: true)]
    private Collection $intakes;

    #[ORM\Column(nullable: true)]
    private ?\DateInterval $duration = null;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Subject::class)]
    private Collection $subjects;

    public function __construct()
    {
        $this->intakes = new ArrayCollection();
        $this->subjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution): static
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * @return Collection<int, Intake>
     */
    public function getIntakes(): Collection
    {
        return $this->intakes;
    }

    public function addIntake(Intake $intake): static
    {
        if (!$this->intakes->contains($intake)) {
            $this->intakes->add($intake);
            $intake->setCourse($this);
        }

        return $this;
    }

    public function removeIntake(Intake $intake): static
    {
        if ($this->intakes->removeElement($intake)) {
            // set the owning side to null (unless already changed)
            if ($intake->getCourse() === $this) {
                $intake->setCourse(null);
            }
        }

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(?\DateInterval $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
            $subject->setCourse($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        if ($this->subjects->removeElement($subject)) {
            // set the owning side to null (unless already changed)
            if ($subject->getCourse() === $this) {
                $subject->setCourse(null);
            }
        }

        return $this;
    }
}
