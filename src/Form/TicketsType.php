<?php

namespace App\Form;

use App\Entity\Tickets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite', NumberType::class,[
                

                'attr'=>['class'=>'input--style-2',
                'placeholder'=>"Quantity",
                'inputmode' => 'numeric',
                'min'=>0,
                'max'=>100 ,
            
                'step' => '1',
                ]
            ])
            
            // ->add('Create', SubmitType::class,[
            //     'attr'=>['class'=>'text-uppercase']
            // ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
