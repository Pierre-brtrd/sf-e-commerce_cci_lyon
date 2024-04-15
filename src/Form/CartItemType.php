<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                    'placeholder' => 'Quantité',
                ],
            ])
            ->add('remove', SubmitType::class, [
                'label' => 'Supprimer',
                'attr' => ['class' => 'btn btn-danger text-light'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
        ]);
    }
}
