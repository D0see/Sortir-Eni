<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class editProfileType extends  AbstractType
{
    public  function  buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
            ->add('maPhoto',FileType::class,[
                'label'=>'Photo de profil',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new Image(
                        maxSize:'5M',
                        maxSizeMessage: "C'est trop gros !",
                        mimeTypesMessage: "Format non valide !"
                    )
                ]
            ])
            ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}