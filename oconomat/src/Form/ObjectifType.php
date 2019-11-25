<?php

namespace App\Form;

use App\Entity\Objectif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('budget')
            ->add('userQuantity')
            ->add('vegetarian', ChoiceType::class, [
                // this verifies if given data is of boolean type
                // during $form->isValid() call; if it is not,
                // verification fails and form is not submitted
                'choices' => [
                    'true' => true,
                    'false' => false
                ]
            ])
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
