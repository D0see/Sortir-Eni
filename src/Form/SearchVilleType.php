<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchVilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', TextType::class, [
                'label' => 'Le nom contient :',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher une ville...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Pas d'entité associée, c'est un formulaire purement "non mappé".
        $resolver->setDefaults([
            'data_class' => SearchVilleType::class,
            'method' => 'GET'
        ]);
    }
}