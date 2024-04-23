<?php

namespace App\Tests\Entity;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Tests\Utils\ValidatorTestTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategorieEntityTest extends KernelTestCase
{
    use ValidatorTestTrait;

    private $databaseTool;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testRepositoryCount(): void
    {
        $this->databaseTool->loadAliceFixture([
            \dirname(__DIR__) . '/Fixtures/CategorieFixtures.yaml',
        ]);

        $categories = self::getContainer()->get(CategorieRepository::class)->count([]);

        $this->assertEquals(11, $categories);
    }

    private function getEntity(): Categorie
    {
        return (new Categorie)
            ->setName('test')
            ->setEnable(true);
    }

    public function testValideEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * @dataProvider provideName
     *
     * @param string $name
     * @param integer $numberError
     * @return void
     */
    public function testInvalideName(string $name, int $numberError): void
    {
        $categorie = $this->getEntity()
            ->setName($name);

        $this->assertHasErrors($categorie, $numberError);
    }

    public function provideName(): array
    {
        return [
            'unique' => [
                'name' => 'Categorie de Test',
                'numberError' => 1,
            ],
            'notBlank' => [
                'name' => '',
                'numberError' => 1,
            ],
            'maxLength' => [
                'name' => str_repeat('a', 256),
                'numberError' => 1,
            ]
        ];
    }
}
