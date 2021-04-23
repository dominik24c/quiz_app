<?php


namespace App\Tests\Entity;


use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserTest extends EntityTest
{

    /**
     * @test
     */
    public function create_correctly_user()
    {
        $errors = Helper::createDummyUserAndSave($this->entityManager, $this->validator);
        $passwordEncoder = new MessageDigestPasswordEncoder();
        $encoder = new UserPasswordEncoder(new EncoderFactory([User::class=>$passwordEncoder]));

        //get user
        $userRecord = $this->entityManager->
                getRepository(User::class)->
                findOneBy(['nick'=>'jam89']);

        //assert
        $this->assertEquals(0, count($errors));
        $this->assertInstanceOf(User::class, $userRecord);
        $this->assertEquals('jam89', $userRecord->getNick());
        $this->assertEquals('James', $userRecord->getFirstName());
        $this->assertEquals('Nelson', $userRecord->getLastName());
        $this->assertEquals('jamesnelson@gmail.com', $userRecord->getEmail());
        $this->assertEquals($encoder->encodePassword($userRecord,'Pass1234'), $userRecord->getPassword());

    }

    /**
     * @test
     */
    public function check_validation_for_user_entity()
    {       //arrange
            $user = Helper::createDummyUser('ca','Jo','Ca','incorrectmail.com','Password12');

            //act
            $errors =  $this->validator->validate($user);

            //assert
            $this->assertCount(4, $errors);

            foreach ($errors as $error){
                    $msg = $error->getMessage();
                    switch ($error->getPropertyPath()){
                        case 'lastName':
                        case 'firstName':
                            $this->assertEquals("This value is too short. It should have 3 characters or more.", $msg);
                            break;
                        case 'email':
                            $this->assertEquals("This value is not a valid email address.", $msg);
                            break;
                        case 'nick':
                            $this->assertEquals("This value is too short. It should have 4 characters or more.", $msg);
                            break;
                    }

            }
    }

}