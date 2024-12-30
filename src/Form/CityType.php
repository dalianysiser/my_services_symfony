<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',null,[
                'label'=>'Name',
                'attr'=>['class'=>'form form-control', 'placeholder'=>'name']
            ])
            ->add('shippingCost', null,[
                'label'=>'Shipping Cost',
                'attr'=>['class'=>'form form-control', 'placeholder'=>'Shipping Cost']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }
}
