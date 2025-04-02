<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options'  => ['label' => 'Nouveau mot de passe','attr'=>['class'=> 'form-control text-dark bg-white','style'=>'color: #000; background-color: #fff;',]],
            'second_options' => ['label' => 'Confirmer le nouveau mot de passe','attr'=>['class'=>'form-control text-dark bg-white','style'=>'color: #000; background-color: #fff;',]],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}