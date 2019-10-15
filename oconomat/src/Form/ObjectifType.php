<?php

namespace App\Form;

use App\Entity\Objectif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('budget')
            ->add('userQuantity')
            ->add('vegetarian')

        //->add('userQuantity')
        //->add('createdAt')
        //->add('updatedAt')
        //->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Objectif::class,
            'csrf_protection' => false,
        ]);
    }
}
