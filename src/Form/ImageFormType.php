<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file',FileType::class, [
                'constraints' => [
                    new Assert\File([
                        'mimeTypes' => ['image/png', 'image/jpeg', 'image/bmp'],
                        'mimeTypesMessage' => "Mauvais format d'image (sont acceptés les fichier .png, .jpg ou .bmp)",
                        'maxSize' => '2M',
                        'maxSizeMessage' => "L'image est trop volumineuse (max: 2mb)"
                    ]),
                ],
                'required'=> false,
                'label' => false,
                'attr' => ['class' => 'form-control mb-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
