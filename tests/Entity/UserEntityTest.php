<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Utils\ValidatorTestTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserEntityTest extends KernelTestCase
{
    use ValidatorTestTrait;

    private $databaseTool;

    /**
     * Méthode exécutée avant chaque test du fichier
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setup();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testRepositoryCount(): void
    {
        $this->databaseTool->loadAliceFixture([
            \dirname(__DIR__) . '/Fixtures/UserFixtures.yaml',
        ]);

        $users = self::getContainer()->get(UserRepository::class)->count([]);

        $this->assertEquals(11, $users);
    }

    private function getEntity(): User
    {
        return (new User)
            ->setFirstName('Test')
            ->setLastName('Test')
            ->setEmail('test-2@test.com')
            ->setPassword('test');
    }

    public function testValideEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * @dataProvider provideEmail
     *
     * @param string $email
     * @param integer $numberError
     * @return void
     */
    public function testInvalideEmail(string $email, int $numberError): void
    {
        $user = $this->getEntity()
            ->setEmail($email);

        $this->assertHasErrors($user, $numberError);
    }

    /**
     * @dataProvider provideName
     *
     * @param string $name
     * @param integer $numberError
     * @return void
     */
    public function testInvalideFirstName(string $name, int $numberError): void
    {
        $user = $this->getEntity()
            ->setFirstName($name);

        $this->assertHasErrors($user, $numberError);
    }

    /**
     * @dataProvider provideName
     *
     * @param string $name
     * @param integer $numberError
     * @return void
     */
    public function testInvalideLastName(string $name, int $numberError): void
    {
        $user = $this->getEntity()
            ->setLastName($name);

        $this->assertHasErrors($user, $numberError);
    }

    public function testGetFullName(): void
    {
        $this->assertEquals('Test Test',  $this->getEntity()->getFullName());
    }

    public function provideEmail(): array
    {
        return [
            'unique' => [
                'email' => 'test@test.com',
                'numberError' => 1,
            ],
            'maxLength' => [
                'email' => str_repeat('a', 180) . '@test.com',
                'numberError' => 1,
            ],
            'notBlank' => [
                'email' =>  '',
                'numberError' => 1,
            ],
            'noValide' => [
                'email' => 'test',
                'numberError' => 1,
            ]
        ];
    }

    public function provideName(): array
    {
        return [
            'maxLength' => [
                'name' => str_repeat('a', 256),
                'numberError' => 1,
            ],
            'notBlank' => [
                'name' => '',
                'numberError' => 1,
            ]
        ];
    }
}
