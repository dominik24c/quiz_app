<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("edit_quiz")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("question")
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=6,max=255)
     */
    private $question;

    /**
     * @ORM\Column(type="integer")
     * @Groups("question")
     * @Assert\NotNull
     * @Assert\Type(type="integer")
     */
    private $points;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="question",cascade={"persist","remove"})
     * @Groups("question")
     * @Assert\Count(min=2)
     */
    private $answers;

    /**
     * @ORM\ManyToOne(targetEntity=Quiz::class, inversedBy="questions")
     */
    private $quiz;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
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

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Quiz
     */
    public function getQuiz():Quiz
    {
        return $this->quiz;
    }

    /**
     * @param Quiz $quiz
     */
    public function setQuiz(Quiz $quiz): void
    {
        $this->quiz = $quiz;
    }


}
