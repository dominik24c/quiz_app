<?php


namespace App\Tests\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Entity\Helper;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseControllerTest extends WebTestCase
{

    protected EntityManagerInterface| null $entityManager;
    protected KernelBrowser $client;

    protected User $user;
    static protected string $NICK = "nick123hex";
    static protected string $EMAIL ='email123@gmail.com';
    static protected string $PASSWORD = 'Pass1234';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
        $this->entityManager->close();
        $this->entityManager = null;
    }
    public function assertRedirectTest(string $uri='/quiz',string $body='Redirecting',int $statusCode=302)
    {
        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body',$body);
        $this->assertSelectorTextContains('body > a',$uri);
    }

    public function createUser():void
    {
        $this->user = Helper::createDummyUser(self::$NICK,email: self::$EMAIL, password: self::$PASSWORD);
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
        $userRepository = static::$container->get(UserRepository::class);
        $this->user = $userRepository->findOneByEmail(self::$EMAIL);
    }


}