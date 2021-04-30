<?php


namespace App\Tests\Repository;


use App\Entity\Answer;
use App\Entity\Quiz;
use App\Tests\Entity\EntityTest;

class AnswerRepositoryTest extends EntityTest
{
    /**
     * @test
     */
    public function testGetAllAnswersByQuiz()
    {
        //arrange
        $answersData = DummyDataForRepository::createDummyDataGetAllAnswersByQuiz($this->entityManager);

        //act
        $quizNotExists = new Quiz();
        $quizNotExists->setId(100);

        /** @var Quiz $quiz */
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(["title"=>DummyDataForRepository::$title]);
        $answerArr = $this->entityManager->getRepository(Answer::class)->getAllAnswersByQuiz($quiz);
        $answersNotExists = $this->entityManager->getRepository(Answer::class)->getAllAnswersByQuiz($quizNotExists);

        //assert
        $this->assertIsArray($answersNotExists);
        $this->assertEmpty($answersNotExists);
        $this->assertIsArray($answerArr);
        $this->assertNotEmpty($answerArr);
        $this->assertCount(8,$answerArr);
        for ($i=0;$i<count($answerArr);$i++){
            $this->assertEquals($answersData[$i][0],$answerArr[$i]->getAnswer());
            $this->assertEquals($answersData[$i][1],$answerArr[$i]->getIsCorrect());
        }
    }
}