<?php

namespace App\Form;

use App\Entity\Reclamations;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('type',ChoiceType::class, [
                'choices'  => [
                    'Information sur votre compte' => 'Information sur votre compte' ,
                    'Information sur vos commandes'=> 'Information sur vos commandes',
                    'Suggestions et remarques sur le site'=> 'Suggestions et remarques sur le site',
                    'Signaler un dysfonctionnement'=> 'Signaler un dysfonctionnement',
                    'Autre' => 'Autre'
                ],
            ])
            ->add('description',TextType::class)
            ->add('dateReclamation', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable'
            ])
            ->add('pieceJointe', FileType::class, [
                'label' => 'Brochure (PDF file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->add('statut',ChoiceType::class, [
                'choices'  => [
                    'Ouvert' => 'Ouvert' ,
                    'Resolu'=> 'Resolu',
                    'En attente'=> 'En attente',
                ],
            ])
            ->add('dateResolution')
            ->add('idAdmin')
            ->add('idUser',EntityType::class,[
                'class' =>Users::class ,
                'choice_label' => 'email',//inajem ikoun id
                'multiple'=> false,
                'expanded'=> false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamations::class,
        ]);
    }
}
