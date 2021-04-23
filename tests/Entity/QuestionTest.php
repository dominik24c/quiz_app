<?php


namespace App\Tests\Entity;


use App\Entity\Question;

class QuestionTest extends EntityTest
{

    /**
     * @test
     */
    public function create_correctly_question_record()
    {
        //arrange
        $question = Helper::createDummyQuestion();

        //act
        $this->entityManager->persist($question);
        $this->entityManager->flush();

        $questionRecord = $this->entityManager->getRepository(Question::class)->findOneBy(['points'=>1]);

        //assert
        $this->assertInstanceOf(Question::class, $questionRecord);
        $this->assertEquals('How many sides does a triangle have?', $questionRecord->getQuestion());
        $this->assertEquals(1, $questionRecord->getPoints());
    }

    /**
     * @test
     */
    public function check_validation_for_question_entity()
    {
        //arrange
        $question = new Question();
        $question->setQuestion("aaa");
        $question->setPoints(1);

        //act
        $errors = $this->validator->validate($question);

        //assert
        $this->assertCount(2, $errors);

        foreach ($errors as $error){
            $msg = $error->getMessage();
            switch ($error->getPropertyPath()){
                case 'question':
                    $this->assertEquals("This value is too short. It should have 6 characters or more.", $msg);
                    break;
                case 'answers':
                    $this->assertEquals("This collection should contain 2 elements or more.", $msg);
                    break;
            }

        }
    }

}