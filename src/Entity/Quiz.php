<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=QuizRepository::class)
 */
class Quiz
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("edit_quiz")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=6,max=100)
     * @Groups("quiz")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min=20)
     * @Groups("quiz")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="quizzes")
     * @ORM\JoinColumn(name="category",referencedColumnName="name")
     * @Assert\NotNull
     * @Groups("quiz")
     */
    private Category $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class,inversedBy="quizzes")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("quiz")
     * @Assert\NotNull
     */
    private $expiredAt;

    /**
     * @ORM\OneToMany(targetEntity=Solution::class, mappedBy="quiz")
     */
    private $solutions;

    /**
     * @ORM\OneToMany(targetEntity=Question::class,mappedBy="quiz", cascade={"persist","remove"})
     * @Groups("quiz")
     * @Assert\Count(min=3)
     */
    private $questions;


    public function __construct()
    {
        $this->solutions = new ArrayCollection();
        $this->questions =  new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->expiredAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeInterface $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * @return Collection|Solution[]
     */
    public function getSolutions(): Collection
    {
        return $this->solutions;
    }



    public function addSolution(Solution $solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions[] = $solution;
            $solution->setQuiz($this);
        }

        return $this;
    }

    public function removeSolution(Solution $solution): self
    {
        if ($this->solutions->removeElement($solution)) {
            // set the owning side to null (unless already changed)
            if ($solution->getQuiz() === $this) {
                $solution->setQuiz(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Collection| Question[]
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param Collection| Question[] $questions
     */
    public function setQuestions(Collection| Array $questions): void
    {
        $this->questions = $questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }

        return $this;
    }

}
