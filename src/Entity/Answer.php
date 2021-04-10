<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=AnswerRepository::class)
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("edit_quiz","solve_quiz")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups("answer")
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=1,max=100)
     */
    private $answer;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("answer")
     * @Assert\NotNull
     * @Assert\Type(type="bool")
     */
    private $isCorrect;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="answers")
     */
    private $question;

    /**
     * @ORM\ManyToMany(targetEntity=Solution::class, mappedBy="answers",cascade={"persist"})
     */
    private $solutions;


    public function __construct()
    {
        $this->solutions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getIsCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): self
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSolutions(): Collection
    {
        return $this->solutions;
    }

    /**
     * @param Collection $solutions
     */
    public function setSolutions(Collection $solutions): void
    {
        $this->solutions = $solutions;
    }

    public function addSolution(Solution $solution)
    {
        if ($this->solutions->contains($solution)) {
            return;
        }
        $this->solutions[] = $solution;
    }
}
