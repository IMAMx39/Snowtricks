<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\Length([
                        'min' => 5, 'minMessage' => 'Le titre doit faire au moins {{ limit }} caractères.',
                        'max' => 30, 'maxMessage' => 'Le titre ne peut pas faire plus de {{ limit }} caractères.'
                    ]),
                    new Assert\NotBlank(['message' => 'Vous devez entrez un titre pour le Trick']),
                ],
                'attr' => ['class' => 'form-control mb-3'],
                'label' => 'Titre'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'label' => 'Description'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageFormType::class,
                'allow_add' => true,
                'mapped' => false,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'label' => false
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'mapped' => true,
                'by_reference' => false,
                'label' => false
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],

                'label' => 'Enregistrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
