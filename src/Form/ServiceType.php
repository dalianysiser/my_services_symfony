<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\SubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('stock')
            ->add('image', FileType::class,[
                'label'=>'image',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new ConstraintsFile([
                      "maxSize"=>"1024k",
                      "mimeTypes"=>[
                        'image/jpg',
                        'image/png',
                        'image/jpeg',
                      ],
                      "maxSizeMessage"=>"Max Size 1024k",
                      "mimeTypesMessage"=>"Invalid Format",
                      "maxSize"=>"1024k"   
                    ])
                ],
            ])
            ->add('subCategories', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
