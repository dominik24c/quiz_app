<?php


namespace App\Tests\Form;


use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Form\Test\TypeTestCase;

class RegistrationTypeTest extends TypeTestCase
{
    private array $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formData = [
        'firstName'=>"John",
        'lastName'=>"Franklin",
        'nick'=>'test123',
        'email'=>'test@gmail.com',
        'password'=>'Pass1234',
        'passwordConfirmation'=>'Pass1234',
        'agreeTerms'=>true
    ];
    }

    /**
     * @test
     */
    public function testSubmitValidRegistration()
    {
        $user = new User();

        $form =  $this->factory->create(RegistrationType::class, $user);

        $expectedUser = $this->fillUserObjectWithFormData();

        $form->submit($this->formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedUser, $user);

    }

    /**
     * @test
     */
    public function testRegistrationFormView()
    {
        $user = $this->fillUserObjectWithFormData();
        $view =  $this->factory->create(RegistrationType::class, $user)
                 ->createView();

        $this->assertArrayHasKey('id', $view->vars['attr']);
        $this->assertSame('form', $view->vars['attr']["id"]);
        $this->assertEquals($this->formData['firstName'],$view->vars["value"]->getFirstName());
        $this->assertEquals($this->formData['lastName'],$view->vars["value"]->getLastName());
        $this->assertEquals($this->formData['nick'],$view->vars["value"]->getNick());
        $this->assertEquals($this->formData['email'],$view->vars["value"]->getEmail());
        $this->assertEquals($this->formData['password'],$view->vars["value"]->getPassword());
        $this->assertEquals($this->formData['passwordConfirmation'],$view->vars["value"]->getPasswordConfirmation());
    }

    public function fillUserObjectWithFormData():User
    {
        $expectedUser = new User();
        $expectedUser->setFirstName($this->formData['firstName']);
        $expectedUser->setLastName($this->formData['lastName']);
        $expectedUser->setPassword($this->formData['password']);
        $expectedUser->setPasswordConfirmation($this->formData['passwordConfirmation']);
        $expectedUser->setEmail($this->formData['email']);
        $expectedUser->setNick($this->formData['nick']);

        return  $expectedUser;
    }
}

