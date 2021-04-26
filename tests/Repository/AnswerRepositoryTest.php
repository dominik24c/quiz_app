<?php


namespace App\Tests\Repository;


use App\Entity\Answer;
use App\Entity\Quiz;
use App\Tests\Entity\EntityTest;
use App\Tests\Entity\Helper;

class AnswerRepositoryTest extends EntityTest
{
    /**
     * @test
     */
    public function testGetAllAnswersByQuiz()
    {
        //arrange
        $category = Helper::createDummyCategory(nameOfCategory: 'History');
        $title = 'History of US';
        $quiz = Helper::createDummyQuiz($category, title:$title);

        $questionsData = [
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
        foreach ($questionsData as $q){
            foreach ($q['answers'] as $a){
                $answersData[]=array($a[0],$a[1]);
            }
        }


        foreach ($questionsData as $item){
            $question = Helper::createDummyQuestion($item['question']);
            $quiz->addQuestion($question);
            foreach ($item['answers'] as $answer){
                $answer = Helper::createDummyAnswer($answer[0],$answer[1]);
                $question->addAnswer($answer);
            }

        }

        $this->entityManager->persist($category);
        $this->entityManager->persist($quiz);
        $this->entityManager->flush();

        //act
        $quizNotExists = new Quiz();
        $quizNotExists->setId(100);

        /** @var Quiz $quiz */
        $quiz = $this->entityManager->getRepository(Quiz::class)->findOneBy(["title"=>$title]);
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