<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null,[
                'attr'=>['class'=>'form form-control']
            ])
            ->add('lastName', null, [
                'attr'=>['class'=>'form form-control']
            ])
            ->add('email', null, [
                'attr'=>['class'=>'form form-control']
            ])
            ->add('phone', null, [
                'attr'=>['class'=>'form form-control']
            ])
            ->add('address', null, [
                'attr'=>['class'=>'form form-control']
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'placeholder'=>'Choose One',
                'choice_label' => 'name',
                'attr'=>['class'=>'form form-control', 'id'=>'order_city']
            ])
            ->add('payOnDelivery', null, [
                'attr'=>[],
                'label'=>'pay on delivery'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
