<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Solution;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $this->createCategories($manager);
        $this->createUsers($manager,$faker);
        $this->createQuizzes($manager,$faker);
        $this->createQuestions($manager,$faker);
        $this->createAnswers($manager,$faker);
        $this->createSolutions($manager,$faker);
        $this->addAnswersToSolution($manager);
    }

    public function createCategories(ObjectManager $manager)
    {
        $categories = [
            'Mathematics', 'Programming','Cryptography',
            'Physics','Artificial Intelligence', 'Biology'
        ];

        foreach ($categories as $category_name){
            $category = new Category();
            $category->setName($category_name);
            $manager->persist($category);
        }

        $manager->flush();
    }

    public function createUsers(ObjectManager $manager, Generator $faker)
    {
        for ($i=0;$i<5;$i++){
            $user = new User();
            $user->setNick($faker->userName);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setPassword($this->encoder->encodePassword($user,"password12"));
            $user->setEmail($faker->email);
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function createQuizzes(ObjectManager $manager, Generator $faker)
    {
        $users = $manager->getRepository(User::class)->findAll();
        $categories =  $manager->getRepository(Category::class)->findAll();

        for($i=0;$i<10;$i++){
            $userIndex = array_rand($users);
            $categoryIndex = array_rand($categories);

            $quiz =  new Quiz();
            $quiz->setTitle($faker->sentence(6));
            $quiz->setDescription($faker->paragraph(3));
            $quiz->setUser($users[$userIndex]);
            $quiz->setCategory($categories[$categoryIndex]);
            $quiz->setCreatedAt(new \DateTime());
            $quiz->setExpiredAt(new \DateTime());
            $manager->persist($quiz);
        }
        $manager->flush();
    }

    public function createQuestions(ObjectManager $manager, Generator $faker)
    {
        $quizzes = $manager->getRepository(Quiz::class)->findAll();
        foreach ($quizzes as $quiz){
            for($i=0;$i<4;$i++){
                $question = new Question();
                $question->setQuiz($quiz);
                $question->setPoints($faker->numberBetween(1,5));
                $question->setQuestion($faker->sentence(10));
                $manager->persist($question);
            }
        }
        $manager->flush();

    }

    public function createAnswers(ObjectManager $manager, Generator $faker)
    {
        $questions = $manager->getRepository(Question::class)->findAll();
        foreach ($questions as $question){
            for($i=0;$i<3;$i++){
                $answer = new Answer();
                $answer->setQuestion($question);
                $answer->setAnswer($faker->sentence(4));
                $answer->setIsCorrect($faker->boolean);
                $manager->persist($answer);
            }
        }
        $manager->flush();
    }

    public function createSolutions(ObjectManager $manager, Generator $faker)
    {
        $users= $manager->getRepository(User::class)->findAll();
        $quizzes = $manager->getRepository(Quiz::class)->findAll();
        foreach ($users as $user){
            $quizIndex = $faker->numberBetween(1,count($quizzes)-1);
            for($i=0;$i<2;$i++){
                $solution = new Solution();
                $solution->setPoints($faker->numberBetween(10,30));
                $solution->setQuiz($quizzes[$quizIndex]);
                $solution->setUser($user);
                $solution->setCreatedAt(new \DateTime());
                $manager->persist($solution);
            }
        }
        $manager->flush();
    }

    public function addAnswersToSolution(ObjectManager $manager)
    {
        $solutions = $manager->getRepository(Solution::class)->findAll();

        foreach ($solutions as $solution){
            $quiz = $solution->getQuiz();
            $answers=$manager->getRepository(Answer::class)->getAllAnswersByQuiz($quiz);
             foreach ($answers as $answer){
                 $solution->addAnswer($answer);
             }
             $manager->persist($solution);
        }
        $manager->flush();
    }
}
