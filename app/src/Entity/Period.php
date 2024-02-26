<?php

namespace App\Entity;

use App\Repository\PeriodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeriodRepository::class)]
class Period
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $start = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $finish = null;

    #[ORM\ManyToOne(inversedBy: 'periods', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Intake $intake = null;

    #[ORM\OneToMany(targetEntity: PeriodToSubject::class, mappedBy: 'period')]
    private Collection $periodToSubjects;

    public function __construct()
    {
        $this->periodToSubjects = new ArrayCollection();
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

    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(\DateTimeImmutable $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getFinish(): ?\DateTimeImmutable
    {
        return $this->finish;
    }

    public function setFinish(\DateTimeImmutable $finish): static
    {
        $this->finish = $finish;

        return $this;
    }

    public function getIntake(): ?Intake
    {
        return $this->intake;
    }

    public function setIntake(?Intake $intake): static
    {
        $this->intake = $intake;

        return $this;
    }

    public function getSubjectsCount(): int
    {
        return $this->periodToSubjects->count();
    }

    public function getPeriodToSubjects(): Collection
    {
        return $this->periodToSubjects;
    }
}
