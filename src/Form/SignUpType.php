<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gregwar\CaptchaBundle\Type\CaptchaType;


class SignUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomUser',TextType::class,[
                'attr'=>['class'=>'input-text',
                'placeholder'=>"First name"
            ]
            ])
            ->add('prenomUser',TextType::class,[
                'attr'=>['class'=>'',
                'placeholder'=>"Last name"
            ]
            ])
            ->add('login',TextType::class,[
                'attr'=>['class'=>'',
                'placeholder'=>"Login"
            ]
            ])
            ->add('password',PasswordType::class,[
                'attr'=>['class'=>'',
                'placeholder'=>"Password"
            ]
            ])
            
            // ->add('mdp',RepeatedType::class, [
            //     'type' => PasswordType::class,
            //     'invalid_message' => 'The password fields must match.',
            //     'options' => ['attr' => ['class' => 'input-field,ii','placeholder'=>"Password"]],
            //         'required' => true,
            //         'first_options'  => ['attr'=>['class'=>'input-field,ii','placeholder'=>"Password"]],
            //         'second_options' => ['attr'=>['class'=>'input-field,ii','placeholder'=>"Repeat Password"]],
                    
            // ])

            ->add('dateNaisUser', DateType::class, [
                'attr'=>['class'=>'',
                'placeholder'=>"Date de naissance",
                            
            ],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                ])
            ->add('email',EmailType::class,[
                'attr'=>['class'=>'',
                'placeholder'=>"Email"
            ]
            ])
            ->add('adresse',TextType::class,[
                'attr'=>['class'=>'',
                'placeholder'=>"Adresse"
            ]
            ])
            ->add('tel',TelType::class,[
                'attr'=>['class'=>'',
                'placeholder'=>"Ex: 00-000-000"
            ]
            ])
/******************************************************** */
            ->add('sexe',ChoiceType::class, [
                'attr'=>['class'=>''],
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
            ])
            
            ->add('role', ChoiceType::class, [
                'attr'=>['class'=>''],
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Freelancer' => 'ROLE_FREELANCER',
                    'Client' => 'ROLE_CLIENT',
                ],
            ])

            ->add('captcha', CaptchaType::class, [
                'label' => ' ',
    
                'attr' => [
                    'placeholder' => 'Entre code'
                ]
            ])

            ->add('SignUp', SubmitType::class,[
                'attr'=>['class'=>'button input']
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
