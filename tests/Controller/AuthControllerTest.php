<?php


namespace App\Tests\Controller;

use App\Tests\FakeData\FakeRecaptcha;
use ReCaptcha\ReCaptcha;

class AuthControllerTest extends BaseControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createUser();

        $fakerecaptcha = new FakeRecaptcha();
        static::$container->set(ReCaptcha::class, $fakerecaptcha);
    }

    //Login Page
    /**
     * @test
     */
    public function testLoginPageWhenYouHaveAlreadyLoggedIn()
    {
        $this->redirectionTestingWhenYouHaveAlreadyLoggedIn('/login');
    }

    /**
     * @test
     */
    public function testLoginPage()
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1','Please sign in');
        $this->assertSelectorExists('input[type="password"]');
        $this->assertSelectorExists('input[type="email"]');
        $this->assertSelectorExists('button.g-recaptcha');
        $this->assertSelectorExists('#form');
    }

    /**
     * @test
     */
    public function testSubmitLoginPageWithValidData()
    {
        $this->LoginPageSubmittingTest(self::$EMAIL, self::$PASSWORD);
        $this->assertRedirectTest();
    }

    /**
     * @test
     */
    public function testSubmitLoginPageWithInvalidData()
    {
        $this->LoginPageSubmittingTest(self::$EMAIL, "IncorrectPassword");
        $this->assertRedirectTest(uri:'/login');
    }


    //registration Page
    /**
     * @test
     */
    public function testRegistrationPage()
    {
        $crawler = $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1','Registration');
        $this->assertSelectorExists('input#registration_firstName');
        $this->assertSelectorExists('input#registration_lastName');
        $this->assertSelectorExists('input#registration_nick');
        $this->assertSelectorExists('input#registration_email');
        $this->assertSelectorExists('input#registration_password');
        $this->assertSelectorExists('input#registration_passwordConfirmation');
        $this->assertSelectorExists('input#registration_agreeTerms');
        $this->assertSelectorExists('button.g-recaptcha');
        $this->assertSelectorExists('#form');
        $this->assertEquals(7,$crawler->filter('.form-error')->count());
    }

    /**
     * @test
     */
    public function testRegistrationPageWhenYouHaveAlreadyLoggedIn()
    {
       $this->redirectionTestingWhenYouHaveAlreadyLoggedIn('/register');
    }

    /**
     * @test
     */
    public function testSubmitRegistrationPageWithValidData()
    {
        $this->RegistrationPageSubmittingTest();
        $this->assertRedirectTest(uri:'/login');
    }
    /**
     * @test
     */
    public function testSubmitRegistrationPageWithInvalidData()
    {
        //too short last name
        $this->RegistrationPageSubmittingTest(lastName: "a");
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    // logout
    /**
     * @test
     */
    public function testLogoutUser()
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/logout');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    // helper methods
    public function redirectionTestingWhenYouHaveAlreadyLoggedIn(string $uri)
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', $uri);
        $this->assertRedirectTest();
    }

    public function assertRedirectTest(string $uri='/quiz',string $body='Redirecting',int $statusCode=302)
    {
        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body',$body);
        $this->assertSelectorTextContains('body > a',$uri);
    }

    public function LoginPageSubmittingTest(string $email,string $password)
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm(
            'Login',
            [
                'email'=>$email,
                'password'=>$password
            ],
        );
    }

    public function RegistrationPageSubmittingTest(
        string $email = "test123@gmail.com", string $password = "Pass1234",
        string $passwordConfirmation = "Pass1234", string $firstName = "Alfred",
        string $lastName = "Nelson", bool $agreeTerms = true, string $nick ="testnickname"
    )
    {
        $this->client->request('GET', '/register');
        $this->client->submitForm(
            'Register',
            [
                'registration[nick]'=>$nick,
                'registration[firstName]'=>$firstName,
                'registration[lastName]'=>$lastName,
                'registration[email]'=>$email,
                'registration[password]'=>$password,
                'registration[passwordConfirmation]'=>$passwordConfirmation,
                'registration[agreeTerms]'=>$agreeTerms
            ],
        );
    }
}