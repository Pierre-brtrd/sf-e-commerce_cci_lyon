<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Filter\ProductFilter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un produit'
                ],
                'required' => false,
            ])
            ->add('min', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'min'
                ],
                'required' => false,
            ])
            ->add('max', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'max'
                ],
                'required' => false,
            ])
            ->add('tags', EntityType::class, [
                'class' => Categorie::class,
                'label' => false,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.enable = true')
                        ->orderBy('c.name', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductFilter::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'validation_groups' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
