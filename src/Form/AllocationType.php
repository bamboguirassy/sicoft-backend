<?php

namespace App\Form;

use App\Entity\Allocation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montantInitial')
            ->add('creditInscrit')
            ->add('engagementAnterieur')
            ->add('montantRestant')
            ->add('exerciceSourceFinancement')
            ->add('compte')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Allocation::class,
        ]);
    }
}
