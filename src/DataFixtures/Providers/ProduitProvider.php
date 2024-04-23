<?php

namespace App\DataFixtures\Providers;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProduitProvider
{
    public function uploadImage(): UploadedFile
    {
        $files = glob(\dirname(__DIR__) . '/images/Produits/*.*');

        $index = array_rand($files);

        $file = new File($files[$index]);
        $file = new UploadedFile($file, $file->getFilename());

        return $file;
    }
}
