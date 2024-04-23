<?php

namespace App\Tests\Entity;

use App\Entity\Produit;
use App\Entity\Taxe;
use App\Repository\ProduitRepository;
use App\Tests\Utils\ValidatorTestTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;

class ProduitEntityTest extends KernelTestCase
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
            \dirname(__DIR__) . '/Fixtures/ProduitFixtures.yaml'
        ]);

        $produits = self::getContainer()->get(ProduitRepository::class)->count([]);

        $this->assertEquals(20, $produits);
    }

    private function getEntity(): Produit
    {
        $taxe = (new Taxe)
            ->setName('TVA 10%')
            ->setEnable(true)
            ->setRate(0.1);

        $file = new File(\dirname(__DIR__) . '/Fixtures/images/Produits/banner-big-data.jpeg');

        return (new Produit)
            ->setTitle('Produit Test')
            ->setPriceHT(199.99)
            ->setShortDescription(str_repeat('a', 100))
            ->setTaxe($taxe)
            ->setEnable(true)
            ->setImage($file);
    }

    public function testValideEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * @dataProvider provideTitle
     *
     * @param string $title
     * @param integer $numberError
     * @return void
     */
    public function testInvalideTitle(string $title, int $numberError): void
    {
        $product = $this->getEntity()
            ->setTitle($title);

        $this->assertHasErrors($product,  $numberError);
    }

    public function provideTitle(): array
    {
        return [
            'unique' => [
                'title' => 'Produit 1',
                'numberError' => 1,
            ],
            'maxLength' => [
                'title' => str_repeat('a', 256),
                'numberError' => 1,
            ],
            'notBlank' => [
                'title' => '',
                'numberError' => 1,
            ]
        ];
    }
}
