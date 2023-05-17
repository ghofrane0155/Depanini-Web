<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomUser',TextType::class,[
            'attr'=>['class'=>'form-control',
            'placeholder'=>"First name"
        ]
        ])
        ->add('prenomUser',TextType::class,[
            'attr'=>['class'=>'form-control',
            'placeholder'=>"Last name"
        ]
        ])
        ->add('login',TextType::class,[
            'attr'=>['class'=>'form-control',
            'placeholder'=>"Username"
        ]
        ])
        // ->add('password',PasswordType::class,[
        //     'attr'=>['class'=>'form-control',
        //     'placeholder'=>"Current Password"
        // ]
        // ])
        ->add('dateNaisUser', DateType::class, [
            'attr'=>['class'=>'form-control date',
        ],
            'widget' => 'single_text',
            'format'=> 'yyyy-MM-dd',
            
            ])
        ->add('email',EmailType::class,[
            'attr'=>['class'=>'form-control email',
            'placeholder'=>"Ex: example@example.com"
        ]
        ])
        ->add('adresse',TextType::class,[
            'attr'=>['class'=>'form-control',
            'placeholder'=>"Adresse"
        ]
        ])
        ->add('tel',TelType::class,[
            'attr'=>['class'=>'form-control mobile-phone-number',
            'placeholder'=>"Ex: 00-000-000"
        ]
        ])
        ->add('sexe',ChoiceType::class, [
            'attr'=>['class'=>'with-gap radio inlineblock'],
            'choices' => [
                'Homme' => 'Homme',
                'Femme' => 'Femme',
            ],
        ])
        ->add('userBio',TextareaType::class, [
            'attr'=>['class'=>'form-control no-resize',
            'placeholder'=>"My Bio"
        ]
        ])
        ->add('image',FileType::class,[
            'mapped' => false,
        ])
       
        ->add('Save_Changes', SubmitType::class,[
            'attr'=>['class'=>'btn btn-primary btn-round']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
