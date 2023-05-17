<?php

namespace App\Form;

use App\Entity\Recrutements;
use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RecrutementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('salaire')
            ->add('description')
            ->add('date')
            ->add('nom');
            // ->add('Ajouter' , SubmitType::class) ;
            // ->add('idclient', EntityType::class,  [
            //     'label' => 'Email',
            //     'class' => Users::class,
            //     'choice_label' => 'email',
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recrutements::class,
        ]);
    }
}




