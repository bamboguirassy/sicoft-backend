<?php

namespace App\Form;

use App\Entity\Tracelog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TracelogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('ressource')
            ->add('operation')
            ->add('oldvalue')
            ->add('newvalue')
            ->add('userEmail')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tracelog::class,
        ]);
    }
}
