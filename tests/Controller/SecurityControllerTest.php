<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    private $databaseTool;
    private $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadAliceFixture([
            \dirname(__DIR__) . '/Fixtures/UserFixtures.yaml',
        ]);
    }

    private function getAdminUser(): User
    {
        return self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'test@test.com']);
    }

    private function getEditorUser(): User
    {
        return self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'editor@test.com']);
    }

    public function testLoginPage(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Se connecter');
    }

    public function testRegisterPage(): void
    {
        $this->client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Créer un compte');
    }

    public function testSubmitFormRegisterPageValideEntity(): void
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form([
            'user[firstName]' => 'John',
            'user[lastName]' => 'Doe',
            'user[email]' => 'john@test.com',
            'user[password][first]' => 'Test1234!',
            'user[password][second]' => 'Test1234!',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects();

        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'john@test.com']);

        $this->assertNotNull($user);
    }

    public function testSubmitFormRegisterPageNonUniqueEmail(): void
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form([
            'user[firstName]' => 'John',
            'user[lastName]' => 'Doe',
            'user[email]' => 'test@test.com',
            'user[password][first]' => 'Test1234!',
            'user[password][second]' => 'Test1234!',
        ]);

        $this->client->submit($form);

        $this->assertSelectorTextContains('.invalid-feedback', 'Cet email est déjà utilisé par un autre compte');
    }

    public function testAdminPageNotLoggedIn(): void
    {
        // $this->client->followRedirects();

        $this->client->request('GET', '/admin/users');

        // $this->assertEquals('/login', $this->client->getResponse()->getBasePath());

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testAdminPageLoggedInAdminUser(): void
    {
        $this->client->loginUser($this->getAdminUser());

        $this->client->request('GET', '/admin/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testAdminPageLoggedInEditorUser(): void
    {
        $this->client->loginUser($this->getEditorUser());

        $this->client->request('GET', '/admin/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
