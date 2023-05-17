<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Entity\Recrutements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('Etat', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',
                    'Confirmé' => 'confirme',
                ],
            ])            ->add('conditions')
            ->add('reference', EntityType::class,  [
                'label' => 'nom',
                'class' => Recrutements::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
