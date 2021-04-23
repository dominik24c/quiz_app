<?php


namespace App\Tests\Entity;


use App\Entity\Solution;

class SolutionTest extends EntityTest
{
    /**
     * @test
     */
    public function create_correctly_solution_record()
    {
        //arrange
        $solution = Helper::createDummySolution();

        //act
        $this->entityManager->persist($solution);
        $this->entityManager->flush();

        $solutionRecord = $this->entityManager->getRepository(Solution::class)->findOneBy(['points'=>1]);

        //assert
        $this->assertInstanceOf(Solution::class, $solutionRecord);
        $this->assertEquals(1, $solutionRecord->getPoints());
    }

}