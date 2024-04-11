<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Taxe;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre du produit'
                ],
                'required' => true,
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Catégories',
                'class' => Categorie::class,
                'placeholder' => "Sélectioner les catégories",
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.enable = :enable')
                        ->setParameter('enable', true)
                        ->orderBy('t.name', 'ASC');
                },
                'multiple' => true,
                'expanded' => false,
                'autocomplete' => true,
                'by_reference' => false,
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Description courte du produit',
                    'rows' => 3,
                ],
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description du produit',
                    'rows' => 10,
                ],
                'required' => false,
            ])
            ->add('priceHT', MoneyType::class, [
                'label' => 'Prix HT',
                'attr' => [
                    'placeholder' => 'Prix HT du produit',
                ],
                'required' => false,
            ])
            ->add('taxe', EntityType::class, [
                'class' => Taxe::class,
                'label' => 'Taxe',
                'placeholder' => 'Sélectionner une taxe',
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.enable = :enable')
                        ->setParameter('enable', true)
                        ->orderBy('t.name', 'ASC');
                },
                'multiple' => false,
                'expanded' => false,
                'autocomplete' => true,
            ])
            ->add('image', VichImageType::class, [
                'label' => 'Image',
                'required' => false,
                'download_uri' => false,
                'image_uri' => true,
                'asset_helper' => true,

            ])
            ->add('enable', CheckboxType::class, [
                'label' => 'Activer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
