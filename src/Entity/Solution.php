<?php

namespace App\Entity;

use App\Repository\SolutionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SolutionRepository::class)
 */
class Solution
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Quiz::class, inversedBy="solutions")
     */
    private $quiz;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="solutions")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Answer::class, inversedBy="solutions",cascade={"persist"})
     * @ORM\JoinColumn(name="solution_answer")
     */
    private $answers;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->points = 0;
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    /**
     * @param Collection $answers
     */
    public function setAnswers(Collection $answers): void
    {
        $this->answers = $answers;
    }

    public function addAnswer(Answer $answer)
    {
        $this->answers[] = $answer;
        $answer->addSolution($this);

        return $this;
    }

}
