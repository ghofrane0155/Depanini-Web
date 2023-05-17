<?php

namespace App\Form;

use DateTimeInterface;
use App\Entity\Produits;
use App\Repository\ProduitRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UpdateProduitType extends AbstractType
{

    private $repository;

    public function __construct(ProduitRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomProduit', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom de produit'
                ]
            ])
            /**->add('categorieProduit', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Categorie '
                ]
            ])**/
            ->add('categorieProduit', ChoiceType::class, [
                'choices' => [
                    '---- Please select ----' => '',
                    'Design' => 'Design',
                    'Education' => 'Education',
                    'Formation' => 'Formation',
                    'Conception' => 'Conception',
                    'Mobile' => 'Mobile',
                    'Montage' => 'Montage',
                    'Publicite' => 'Publicite',
                    'Gaming' => 'Gaming',
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control show-tick'
                ]
            ])
            ->add('prixProduit', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prix '
                ]
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Description'
                ]
            ])
            
            ->add('image', FileType::class, [
                'mapped' => false

            ])
            /*->add('Modifier', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-raised btn-primary btn-round waves-effect'
                ]
            ])*/;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_produit' => Produits::class,
        ]);
    }
}
