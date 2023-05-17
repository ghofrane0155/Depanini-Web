<?php

namespace App\Form;

use App\Entity\Events;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EventDetailsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomevent',TextType::class,[
            'attr'=>['class'=>'input--style-2',
            'placeholder'=>"Event Name"
        ]
        ])
        ->add('nombrelimevent',TextType::class,[
            'attr'=>['class'=>'input--style-2',
            'placeholder'=>"Limeted Number"
        ]
        ])
        ->add('lieuevent',TextType::class,[
            'attr'=>['class'=>'input--style-2',
            'placeholder'=>"Event place"
        ]
        ])
        ->add('datedebutevent', DateType::class, [
            'attr'=>['class'=>'input--style-2 js-datepicker',
            'placeholder'=>"Ex: 30/07/2000"
        ],
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            ])
        ->add('datefinevent', DateType::class, [
            'attr'=>['class'=>'input--style-2 js-datepicker',
            'placeholder'=>"Ex: 30/07/2000"
        ],
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            ])
        ->add('organisateurevent',TextType::class,[
            'attr'=>['class'=>'input--style-2',
            'placeholder'=>"Event Organiser"
        ]
        ])
        ->add('descriptionevent',TextType::class,[
            'attr'=>['class'=>'input--style-2',
            'placeholder'=>"Description"
        ]
        ])
        // ->add('image', FileType::class, [
        //     'mapped' => false

        // ])
        ->add('prixevent',TextType::class,[
            'attr'=>['class'=>'input--style-2',
            'placeholder'=>"Event price"
        ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }
}
