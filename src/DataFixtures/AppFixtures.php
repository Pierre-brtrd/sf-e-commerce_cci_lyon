<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Taxe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new User)
            ->setFirstName('Pierre')
            ->setLastName('Bertrand')
            ->setEmail('admin@test.com')
            ->setPassword(
                $this->hasher->hashPassword(new User, 'Test1234!'),
            )
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) {
            $user = (new User)
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setEmail($this->faker->unique()->email())
                ->setPassword(
                    $this->hasher->hashPassword(new User, 'Test1234!'),
                )
                ->setRoles(
                    $this->faker->randomElements(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_EDITOR'])
                );

            $manager->persist($user);
        }

        $tva = (new Taxe)
            ->setName('TVA 10%')
            ->setRate(0.10)
            ->setEnable(true);

        $manager->persist($tva);

        $tva = (new Taxe)
            ->setName('TVA 20%')
            ->setRate(0.20)
            ->setEnable(true);

        $manager->persist($tva);

        $categoriesArray = [
            'Mobile', 'Desktop',
            'Tablette', 'TV', 'Ipad',
            'Smartphone', 'Console',
            'Jeux', 'Mac', 'Accessoires'
        ];

        foreach ($categoriesArray as $categorieName) {
            $categorie = (new Categorie)
                ->setName($categorieName)
                ->setEnable(true);

            $manager->persist($categorie);

            $categories[] = $categorie;
        }

        for ($i = 0; $i < 50; $i++) {
            $produit = (new Produit)
                ->setTitle($this->faker->unique()->word())
                ->setEnable($this->faker->boolean())
                ->setPriceHT($this->faker->randomFloat(2, 1, 1000))
                ->setShortDescription($this->faker->sentence(20, true))
                ->setDescription(file_get_contents("https://loripsum.net/api/3/short/link/ul"))
                ->setTaxe($tva)
                ->setImage($this->uploadImage());

            for ($j = 0; $j < $this->faker->numberBetween(1, 4); $j++) {
                $produit
                    ->addCategory($categories[$this->faker->randomDigit()]);
            }

            $manager->persist($produit);
        }

        $manager->flush();
    }

    private function uploadImage(): UploadedFile
    {
        $files = glob(__DIR__ . '/images/Produits/*.*');

        $index = array_rand($files);

        $file = new File($files[$index]);
        $file = new UploadedFile($file, $file->getFilename());

        return $file;
    }
}
