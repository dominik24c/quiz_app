<?php


namespace App\Tests\Entity;


use App\Entity\Category;

class CategoryTest extends EntityTest
{
    /**
     * @test
     */
    public function create_correctly_category_record()
    {
        //arrange
        $category = Helper::createDummyCategory();

        //act
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $categoryRecord = $this->entityManager->getRepository(Category::class)->findOneBy(['name'=>'Programming']);

        //assert
        $this->assertInstanceOf(Category::class, $categoryRecord);
        $this->assertEquals('Programming', $categoryRecord->getName());
    }

    /**
     * @test
     */
    public function check_validation_for_category_entity()
    {
        //arrange
        $category = new Category();
        $category->setName('cat');

        //act
        $errors = $this->validator->validate($category);

        //assert
        $this->assertCount(1, $errors);

        foreach ($errors as $error){
            $msg = $error->getMessage();
            switch ($error->getPropertyPath()){
                case 'name':
                    $this->assertEquals("This value is too short. It should have 4 characters or more.", $msg);
                    break;
            }

        }
    }
}