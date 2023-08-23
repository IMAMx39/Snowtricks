<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class VideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename', TextType::class, [
                'attr' => [
                    'placeholder' => "Entrez l'URL d'une vidéo Youtube",
                    'class' => 'form-control mb-3'
                ],
                'required' => false,
                'label' => false,
                'constraints' => [
                    new Regex(['pattern' => "^((http(s)?:\\/\\/)?((w){3}.)?youtu(be|.be)?(\\.com)?\\/.+)|(#TO_DELETE#)^", 'message' => 'L\'URL de la vidéo entrée n\'est pas valide ! Nous acceptons les vidéos provenant de Youtube uniquement.'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
