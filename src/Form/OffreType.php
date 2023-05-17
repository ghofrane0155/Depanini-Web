<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Offres;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('prixOffre')
            ->add('descriptionOffre')
            ->add('nomOffre')
            ->add('localisationOffre')
            ->add('categorie', EntityType::class,  [
                'label' => 'Categorie',
                'class' => Categories::class,
                'choice_label' => 'nomCategorie',
            ])
            // ->add('imageOffre',FileType::class, [
            //     'label' => 'Image de votre offre  (JPG,JPAG,PNG)',
    
            //     // unmapped means that this field is not associated to any entity property
            //     'mapped' => false,

            //     'required' => false,
    
            //     // unmapped fields can't define their validation using annotations
            //     // in the associated entity, so you can use the PHP constraint classes
            //     'constraints' => [
            //         new File([
            //             'maxSize' => '1024k',
            //             'mimeTypes' => [
            //                 'image/png',
            //                 'image/jpg',
            //                 'image/jpeg',
            //             ],
            //             'mimeTypesMessage' => 'déposez votre image',
            //         ])
            //     ],
            // ])
            ->add('image',FileType::class,[
                'mapped' => false,
            ])
            ->add('typeOffre',ChoiceType::class, [
                'choices' => [
                    'typeOffre'=>'',
                    'Enligne' => 'Enligne',
                    'Presentiel' => 'Presentiel',
                ]])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offres::class,
        ]);
    }
}