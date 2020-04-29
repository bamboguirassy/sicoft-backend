<?php

namespace App\Form;

use App\Entity\ExerciceSourceFinancement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExerciceSourceFinancementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montantInitial')
            ->add('montantRestant')
            ->add('sourceFinancement')
            ->add('budget')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExerciceSourceFinancement::class,
        ]);
    }
}
