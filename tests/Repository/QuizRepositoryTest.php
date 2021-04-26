<?php


namespace App\Tests\Repository;


use App\Entity\Quiz;
use App\Tests\Entity\EntityTest;
use App\Tests\Entity\Helper;
use PHPUnit\Util\Exception;

class QuizRepositoryTest extends EntityTest
{
    /** @test */
    public function testGetTheMostPopularQuizzes()
    {
        //arrange
        $user = Helper::createDummyUser();
        $category = Helper::createDummyCategory();

        $this->entityManager->persist($user);
        $this->entityManager->persist($category);

        $quizzesData =[
            ['title'=>'quiz1','numberOfSolvedQuiz'=>9],
            ['title'=>'quiz2','numberOfSolvedQuiz'=>3],
            ['title'=>'quiz3','numberOfSolvedQuiz'=>1]
        ];
        $titles = array();
        $quizzes = array();

        foreach ($quizzesData as $q){
            $titles[]= $q['title'];
            $quiz = Helper::createDummyQuiz($category,$q['title']);
            $quizzes[] = $quiz;
            $this->entityManager->persist($quiz);
        }
        for($i=0;$i<count($quizzes);$i++){
            for($j=0; $j<$quizzesData[$i]['numberOfSolvedQuiz'];$j++){
                $this->entityManager->persist(Helper::createDummySolution(user: $user, quiz: $quizzes[$i]));
            }
        }

        $this->entityManager->flush();

        //act
        $quizzes2 =$this->entityManager->getRepository(Quiz::class)
            ->getTheMostPopularQuizzes(2);
        $quizzes3 =$this->entityManager->getRepository(Quiz::class)
            ->getTheMostPopularQuizzes(3);

        //assert

        $this->assertIsArrayAndNotEmpty($quizzes2);
        $this->assertCount(2,$quizzes2);
        $this->assertTitleQuizzes($quizzes2,$titles,false);

        $this->assertIsArrayAndNotEmpty($quizzes3);
        $this->assertCount(3,$quizzes3);
        $this->assertTitleQuizzes($quizzes3,$titles,false);
    }

    /** @test */
    public function testGetQuizzesByUser():void
    {
        //arrange
        $category = Helper::createDummyCategory();
        $this->entityManager->persist($category);


        $quizzesTitleUserA =['quiz1','quiz2','quiz94'];
        $quizzesTitleUserB =['quiz3','quiz9'];
        $usersData = [[
                'nick'=>'alfredo',
                'email'=>'alfredo@gmail.com'
            ],[
                'nick'=>'tyler3',
                'email'=>'tyler3@gmail.com'
            ]
        ];

        $users = array();

        foreach ($usersData as $userData){
            $user=Helper::createDummyUser(nick:$userData['nick'],email: $userData['email']);
            $users[] = $user;
            $this->entityManager->persist($user);
        }

        foreach ($quizzesTitleUserA as $title){
            $this->entityManager->persist(Helper::createDummyQuiz($category,$title, user: $users[0]));
        }
        foreach ($quizzesTitleUserB as $title){
            $this->entityManager->persist(Helper::createDummyQuiz($category,$title,user:$users[1]));
        }
        $this->entityManager->flush();

        //act
        $quizzesUserA = $this->entityManager->getRepository(Quiz::class)
            ->getQuizzesByUser($users[0],5,0);
        $quizzesUserB = $this->entityManager->getRepository(Quiz::class)
            ->getQuizzesByUser($users[1],5,0);

        //assert
        $this->assertIsArrayAndNotEmpty($quizzesUserA);
        $this->assertCount(count($quizzesTitleUserA),$quizzesUserA);
        $this->assertTitleQuizzes($quizzesUserA,$quizzesTitleUserA);

        $this->assertIsArrayAndNotEmpty($quizzesUserB);
        $this->assertCount(count($quizzesTitleUserB),$quizzesUserB);
        $this->assertTitleQuizzes($quizzesUserB,$quizzesTitleUserB);
    }

    /** @test */
    public function testGetQuizzesByTitle():void
    {
        //arrange
        $searchedTitle = 'Quiz';
        $searchedQuizzesTitle = ['Quiz', 'Quiz A', 'QuizB','QuizC','QuizD','QuizE2'];
        $quizzesTitles = array_merge($searchedQuizzesTitle, ['1Quiz', '2Quiz','Title']);

        $category = Helper::createDummyCategory();
        $this->entityManager->persist($category);

        foreach ($quizzesTitles as $title){
            $this->entityManager->persist(Helper::createDummyQuiz($category,$title));
        }
        $this->entityManager->flush();

        //act
        $quizzesArr1 = $this->entityManager->getRepository(Quiz::class)
            ->getQuizzesByTitle(title:$searchedTitle);

        $quizzesArr2 = $this->entityManager->getRepository(Quiz::class)
            ->getQuizzesByTitle(title:$searchedTitle,offset: 5);

        $quizzesArr3 = $this->entityManager->getRepository(Quiz::class)
            ->getQuizzesByTitle(title:$searchedTitle,offset: 2, pageSize: 2);

        $quizzesArr4 = $this->entityManager->getRepository(Quiz::class)
            ->getQuizzesByTitle(flag: false);

        $quizzesArr5 = $this->entityManager->getRepository(Quiz::class)
            ->getQuizzesByTitle(flag: false, title: $searchedTitle);

        //assert
        $this->assertIsArrayAndNotEmpty($quizzesArr1);
        $this->assertCount(5,$quizzesArr1);

        $this->assertIsArrayAndNotEmpty($quizzesArr2);
        $this->assertCount(1,$quizzesArr2);

        $this->assertIsArrayAndNotEmpty($quizzesArr3);
        $this->assertCount(2,$quizzesArr3);

        $this->assertIsArrayAndNotEmpty($quizzesArr4);
        $this->assertCount(count($quizzesTitles),$quizzesArr4);

        $this->assertIsArrayAndNotEmpty($quizzesArr5);
        $this->assertCount(count($searchedQuizzesTitle),$quizzesArr5);
    }

    public function assertIsArrayAndNotEmpty(array $arr):void
    {
        $this->assertIsArray($arr);
        $this->assertNotEmpty($arr);
    }

    public function assertTitleQuizzes(array $quizzes, array $titles, $isArrayOfQuiz = true):void
    {
        if(count($quizzes)>count($titles)){
            throw new Exception('Length of quizzes array is greater than titles!');
        }

        for($i=0;$i< count($quizzes);$i++){
            if($isArrayOfQuiz){
                $this->assertEquals($titles[$i],$quizzes[$i]->getTitle());
            }else{
                $this->assertEquals($titles[$i],$quizzes[$i]['title']);
            }
        }
    }
}
