<?php


namespace App\Tests\Entity;


use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Solution;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Helper
{
    static public function createDummyUser(string $nick = "jam89", string $firstName = "James",
                                           string $lastName="Nelson", string $email = "jamesnelson@gmail.com",
                                           string $password = "Pass1234"): User
    {
        $passwordEncoder = new MessageDigestPasswordEncoder();
        $encoder = new UserPasswordEncoder(new EncoderFactory([User::class=>$passwordEncoder]));
        $user = new User();
        $user->setNick($nick);
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPassword($encoder->encodePassword($user,$password));

        return $user;
    }

    static public function createDummyUserAndSave(EntityManagerInterface $entityManager,ValidatorInterface $validator = null): ConstraintViolationListInterface | null
    {
        $user = Helper::createDummyUser();
        if ($validator != null){
            $errors =  $validator->validate($user);
        }else{
            $errors=null;
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $errors;
    }

    static public function createDummyCategory(string $nameOfCategory="Programming"): Category
    {
        $category = new Category();
        $category->setName($nameOfCategory);

        return $category;
    }

    static public function createDummyQuiz(Category $category, string $title = "New Quiz",
                                    string $description = "example of description 1234 lorem ipsum",
                                    \DateTime $expiredAt = null):Quiz
    {
        $createdAt = new \DateTime();

        if ($expiredAt == null){
            $expiredAt = new \DateTime();
        }

        $quiz = new Quiz();
        $quiz->setTitle($title);
        $quiz->setDescription($description);
        $quiz->setCategory($category);
        $quiz->setCreatedAt($createdAt);
        $quiz->setExpiredAt($expiredAt);

        return $quiz;

    }

    static public function createDummyQuestion(string $questionText="How many sides does a triangle have?", int $points = 1): Question
    {
        $question = new Question();
        $question->setQuestion($questionText);
        $question->setPoints($points);
        return $question;
    }

    static public function createDummyAnswer(string $answerText="three", bool $isCorrect = true): Answer
    {
        $answer = new Answer();
        $answer->setAnswer($answerText);
        $answer->setIsCorrect($isCorrect);
        return $answer;
    }

    static public function createDummySolution(int $points = 1)
    {
        $solution = new Solution();
        $solution->setPoints($points);

        return $solution;
    }
}