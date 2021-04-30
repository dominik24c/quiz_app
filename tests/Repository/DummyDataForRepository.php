<?php


namespace App\Tests\Repository;


use App\Tests\Entity\Helper;
use Doctrine\ORM\EntityManagerInterface;

class DummyDataForRepository
{
    static public array $titles;
    static public array $searchedTitlesForQuizRoute;
    static public array $titlesForQuizRoute;

    static public string $title = 'History of US';
    static public array $questionsData;

    static public function createDummyDataForGetTheMostPopularQuizzes(EntityManagerInterface $entityManager): void
    {
        $user = Helper::createDummyUser();
        $category = Helper::createDummyCategory();

        $entityManager->persist($user);
        $entityManager->persist($category);

        $quizzesData =[
            ['title'=>'quiz1','numberOfSolvedQuiz'=>9],
            ['title'=>'quiz2','numberOfSolvedQuiz'=>3],
            ['title'=>'quiz3','numberOfSolvedQuiz'=>1]
        ];
        self::$titles = array();
        $quizzes = array();

        foreach ($quizzesData as $q){
            self::$titles[]= $q['title'];
            $quiz = Helper::createDummyQuiz($category,$q['title']);
            $quizzes[] = $quiz;
            $entityManager->persist($quiz);
        }
        for($i=0;$i<count($quizzes);$i++){
            for($j=0; $j<$quizzesData[$i]['numberOfSolvedQuiz'];$j++){
                $entityManager->persist(Helper::createDummySolution(user: $user, quiz: $quizzes[$i]));
            }
        }

        $entityManager->flush();
    }

    static public function createDummyDataForGetQuizzesByTitle(EntityManagerInterface $entityManager): void
    {
        self::$searchedTitlesForQuizRoute = ['Quiz', 'Quiz A', 'QuizB','QuizC','QuizD','QuizE2'];
        self::$titlesForQuizRoute = array_merge(self::$searchedTitlesForQuizRoute, ['1Quiz', '2Quiz','Title']);

        $category = Helper::createDummyCategory();
        $entityManager->persist($category);

        foreach (self::$titlesForQuizRoute as $title){
            $entityManager->persist(Helper::createDummyQuiz($category,$title));
        }
        $entityManager->flush();

    }

    static public function createDummyDataGetAllAnswersByQuiz(EntityManagerInterface $entityManager): array
    {
        $category = Helper::createDummyCategory(nameOfCategory: 'History');
        $quiz = Helper::createDummyQuiz($category, title:self::$title);

        self::$questionsData = [
            [
                'question'=>'Who was the first president of the USA?',
                'answers'=>[
                    ['Benjamin Franklin',false],
                    ['George Washington', true],
                    ['Ronald Reagan', false]
                ]
            ],
            [
                'question'=>'When is independence day celebrated in USA',
                'answers'=>[
                    ['fourth of june',false],
                    ['fourth of july',true],
                    ['fifth of july',false]
                ]
            ],
            [
                'question'=>'Who won the battle of Savannah 1779?',
                'answers'=>[
                    ['United States', false],
                    ['The Great Britain', true]
                ]
            ]
        ];

        $answersData = array();
        foreach (self::$questionsData as $q){
            foreach ($q['answers'] as $a){
                $answersData[]=array($a[0],$a[1]);
            }
        }


        foreach (self::$questionsData as $item){
            $question = Helper::createDummyQuestion($item['question']);
            $quiz->addQuestion($question);
            foreach ($item['answers'] as $answer){
                $answer = Helper::createDummyAnswer($answer[0],$answer[1]);
                $question->addAnswer($answer);
            }

        }

        $entityManager->persist($category);
        $entityManager->persist($quiz);
        $entityManager->flush();

        return $answersData;
    }
}