<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity("nick",message="This nickname has already been taken!")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=40)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=40)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string",length=40)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=40)
     */
    private $lastName;

    /**
     * @ORM\Column (type="string",length=50,unique=true)
     * @Assert\NotBlank
     * @Assert\Length(min=4, max=50)
     */
    private $nick;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(min=5, max=180)
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min=8, max=255)
     */
    private $password;

    private $passwordConfirmation;

    /**
     * @ORM\OneToMany(targetEntity=Solution::class, mappedBy="user")
     */
    private $solutions;

    /**
     * @ORM\OneToMany(targetEntity=Quiz::class,mappedBy="user")
     */
    private $quizzes;

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
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
     * @return mixed
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * @param mixed $nick
     */
    public function setNick($nick): void
    {
        $this->nick = $nick;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
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
            $solution->setUser($this);
        }

        return $this;
    }

    public function removeSolution(Solution $solution): self
    {
        if ($this->solutions->removeElement($solution)) {
            // set the owning side to null (unless already changed)
            if ($solution->getUser() === $this) {
                $solution->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getQuizzes():Collection
    {
        return $this->quizzes;
    }

    /**
     * @param Collection $quizzes
     */
    public function setQuizzes(Collection $quizzes): void
    {
        $this->quizzes = $quizzes;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getPasswordConfirmation()
    {
        return $this->passwordConfirmation;
    }

    /**
     * @param mixed $passwordConfirmation
     */
    public function setPasswordConfirmation($passwordConfirmation): void
    {
        $this->passwordConfirmation = $passwordConfirmation;
    }


}
