<?php


namespace App\Tests\Controller;

use App\Entity\Answer;
use App\Entity\Quiz;
use App\Entity\Solution;
use App\Tests\Repository\DummyDataForRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QuizControllerTest extends BaseControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createUser();
    }

    /** @test */
    public function testQuizPageIfUserIsNotLoggedIn(): void
    {
        //act
        $this->client->request('GET','/quiz');
        // redirect to login
        $this->assertRedirectTest('/login');
    }

    /** @test */
    public function testQuizPageIfUserIsLoggedIn(): void
    {
        //arrange
        DummyDataForRepository::createDummyDataForGetQuizzesByTitle($this->entityManager);
        $this->client->loginUser($this->user);

        //act
        $crawler = $this->client->request('GET','/quiz');

        //assert
        $this->assertCount(5,$crawler->filter('.quiz'));
        $this->assertCount(1,$crawler->filter('#paginator'));

        $crawler = $this->client->request('GET','/quiz?page=2');
        $this->assertCount(4, $crawler->filter('.quiz'));
    }

    /** @test */
    public function testSolveQuizPageIfUserIsNotLoggedIn(): void
    {
        DummyDataForRepository::createDummyDataForGetQuizzesByTitle($this->entityManager);
        $quiz = $this->entityManager->getRepository(Quiz::class)
            ->findOneBy(['title'=>DummyDataForRepository::$titlesForQuizRoute[0]]);
        //act
        $this->client->request('GET','/quiz/'.$quiz->getId().'/solve');

        //assert
        $this->assertRedirectTest('/login');
    }

    /** @test */
    public function testSolveQuizPageIfQuizDontExist()
    {
        //arrange
        DummyDataForRepository::createDummyDataForGetQuizzesByTitle($this->entityManager);
        $this->client->loginUser($this->user);

        //act
        $this->client->request('GET','/quiz/eowjewojoew/solve');

        //assert
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /** @test */
    public function testSolveQuizPageIfUserIsLoggedIn(): void
    {
        //arrange
        DummyDataForRepository::createDummyDataForGetQuizzesByTitle($this->entityManager);
        $quiz = $this->entityManager->getRepository(Quiz::class)
            ->findOneBy(['title'=>DummyDataForRepository::$titlesForQuizRoute[0]]);
        $this->client->loginUser($this->user);

        //act
        $crawler = $this->client->request('GET','/quiz/'.$quiz->getId().'/solve');

        //assert
        $description = '#description > ';
        $this->assertEquals('Quiz', $crawler->filter('h1')->first()->text());
        $this->assertEquals('Number of questions: 0', $crawler->filter($description."p")->eq(1)->text());
        $this->assertEquals('Category: Programming', $crawler->filter($description."div")->first()->text());
        $this->assertEquals('Solved by: 0 users', $crawler->filter($description."div")->eq(1)->text());

    }

    /** @test  */
    public function testGetQuestionsIfUserIsNotLoggedIn()
    {
        DummyDataForRepository::createDummyDataGetAllAnswersByQuiz($this->entityManager);
        $quiz = $this->entityManager->getRepository(Quiz::class)
            ->findOneBy(['title'=>DummyDataForRepository::$title]);
        $this->client->request('GET','/quiz/'.$quiz->getId().'/get-questions');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    /** @test  */
    public function testGetQuestionsIfQuizDoesntExists()
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET','/quiz/45/get-questions');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /** @test */
    public function testGetQuestionsIfUserIsLoggedIn()
    {
        DummyDataForRepository::createDummyDataGetAllAnswersByQuiz($this->entityManager);
        $quiz = $this->entityManager->getRepository(Quiz::class)
            ->findOneBy(['title'=>DummyDataForRepository::$title]);
        $this->client->loginUser($this->user);
        $this->client->request('GET','/quiz/'.$quiz->getId().'/get-questions');
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(),true);
//        var_dump($responseData);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertCount(count(DummyDataForRepository::$questionsData),$responseData);

        $i=0;
        foreach ($responseData as $question){
            $this->assertEquals(DummyDataForRepository::$questionsData[$i]['question'],$question['question']);
            $answers = $question['answers'];
            $j=0;
            foreach ($answers as $answer){
                $this->assertEquals(DummyDataForRepository::$questionsData[$i]['answers'][$j][0], $answer['answer']);
                $j++;
            }
            $i++;
        }

    }

    /** @test */
    public function testSaveUserSolutionIfUserIsNotLoggedIn()
    {
        $this->client->request('POST','/quiz/34/solve',array(),array(),
            array('Content-Type' => 'application/json'),null
        );

        $this->assertEquals(302,$this->client->getResponse()->getStatusCode());
    }

    /** @test */
    public function testSaveUserSolutionIfUserIsLoggedIn()
    {
        DummyDataForRepository::createDummyDataGetAllAnswersByQuiz($this->entityManager);
        $quiz = $this->entityManager->getRepository(Quiz::class)
            ->findOneBy(['title'=>DummyDataForRepository::$title]);
        $this->client->loginUser($this->user);
        $data =[];
        foreach (DummyDataForRepository::$questionsData as $question){
            foreach ($question['answers'] as $answer){
                array_push($data,['id'=> $answer[0]]);
                break;
            }
        }

        $answers= [];
        foreach ($data as $answer){
            $a = $this->entityManager->getRepository(Answer::class)
                ->findOneBy(['answer'=>$answer]);
            $answers[] = ['id'=>$a->getId()];
        }
        $jsonData = json_encode($answers);


        $this->client->request('POST','/quiz/'.$quiz->getId().'/solve',array(),array(),
            array('Content-Type' => 'application/json'),$jsonData
        );

        $solution = $this->entityManager->getRepository(Solution::class)
            ->findOneBy(['user'=>$this->user->getId()]);
        $this->assertEquals(Solution::class, get_class($solution));
        $answers = $solution->getAnswers();
        $this->assertCount(3,$answers);
    }
}
