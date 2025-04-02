<?php

namespace App\Form;

use App\Model\SortieFiltersModel;
use App\Entity\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SortieFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'label' => 'Site',
                'required' => false,
                'placeholder' => 'Filtrer par site',
            ])
            ->add('contenu', TextType::class, [
                'label' => 'Contenu',
                'required' => false,
                'attr' => ['placeholder' => 'Filtrer par contenu'],
            ])
            ->add('debut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Début',
                'required' => false,
            ])
            ->add('fin', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fin',
                'required' => false,
            ])
            ->add('sortieQueJOrganise', CheckboxType::class, [
                'label' => 'Sortie que j\'organise',
                'required' => false,
            ])
            ->add('sortieOuJeSuisInscrit', CheckboxType::class, [
                'label' => 'Sortie où je suis inscrit',
                'required' => false,
            ])
            ->add('sortieOuJeNeSuisPasInscrit', CheckboxType::class, [
                'label' => 'Sortie où je ne suis pas inscrit',
                'required' => false,
            ])
            ->add('sortiePassees', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SortieFiltersModel::class,
            'method' => 'GET'
        ]);
    }
}