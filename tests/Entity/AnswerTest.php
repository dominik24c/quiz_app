<?php


namespace App\Tests\Entity;


use App\Entity\Answer;
use Symfony\Component\Validator\Constraints\Length;

class AnswerTest extends EntityTest
{

    /**
     * @test
     */
    public function create_correctly_answer_record()
    {
        //arrange
        $answer = Helper::createDummyAnswer();

        //act
        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        $answerRecord = $this->entityManager->getRepository(Answer::class)->findOneBy(['answer'=>'three']);

        //assert
        $this->assertInstanceOf(Answer::class, $answerRecord);
        $this->assertEquals('three', $answerRecord->getAnswer());
        $this->assertEquals(true, $answerRecord->getIsCorrect());
    }

    /**
     * @test
     */
    public function check_validation_for_answer_entity()
    {
        //arrange
        $answer = new Answer();
        $answer->setAnswer('');
        $answer->setIsCorrect(false);

        //act
        $errors = $this->validator->validate($answer);

        //assert
        $this->assertCount(2, $errors);

        foreach ($errors as $error){
            $msg = $error->getMessage();
            switch ($error->getPropertyPath()){
                case 'answer':
                    if ($error->getConstraint() instanceof Length){
                        $this->assertEquals("This value is too short. It should have 1 character or more.", $msg);
                    }
                    else{
                        $this->assertEquals("This value should not be blank.", $msg);
                    }
                    break;
            }

        }
    }

}