<?php


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * @test
     */
    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1','The Most popular quizzes:');
    }
    
    /**
     * @test
     */
    public function testContact()
    {
        $this->client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1','Contact');
    }
}
