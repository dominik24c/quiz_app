<?php


namespace App\Tests\Entity;


use App\Entity\Quiz;

class QuizTest extends EntityTest
{
    /** 
     * @test 
     */
    public function create_correctly_quiz_record()
    {
        //arrange
        $category = Helper::createDummyCategory();

        $quiz = Helper::createDummyQuiz($category);

        //act
        $this->entityManager->persist($category);
        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        $quizRecord = $this->entityManager->getRepository(Quiz::class)->findOneBy(['title'=>'New Quiz']);

        //assert
        $this->assertInstanceOf(Quiz::class, $quizRecord);
        $this->assertEquals("New Quiz", $quizRecord->getTitle());
        $this->assertEquals("example of description 1234 lorem ipsum",$quizRecord->getDescription());
        $this->assertEquals('Programming', $quizRecord->getCategory()->getName());
    }
    
    /**
    *@test
    */
    public function check_validation_for_quiz_entity()
    {
        //arrange
        $category = Helper::createDummyCategory();
        $quiz = Helper::createDummyQuiz($category,'qwz','description');

        //act
        $errors =  $this->validator->validate($quiz);

        //assert
        $this->assertCount(3, $errors);

        foreach ($errors as $error){
            $msg = $error->getMessage();
            switch ($error->getPropertyPath()){
                case 'title':
                    $this->assertEquals("This value is too short. It should have 6 characters or more.", $msg);
                    break;
                case 'description':
                    $this->assertEquals("This value is too short. It should have 20 characters or more.", $msg);
                    break;
                case 'questions':
                    $this->assertEquals("This collection should contain 3 elements or more.", $msg);
                    break;
            }

        }
    }
}