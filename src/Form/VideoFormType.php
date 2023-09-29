<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;


class VideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $matchYoutube = str_replace('/', '\/', preg_quote("https://www.youtube.com/watch?v="));
        $matchDailyM = str_replace('/', '\/', preg_quote( "https://www.dailymotion.com/video/"));
        $builder
            ->add('filename', TextType::class, [
                'attr' => [
                    'placeholder' => "Entrez l'URL d'une vidéo Youtube",
                    'class' => 'form-control mb-3'
                ],
                'required' => false,
                'label' => false,
                'constraints' => [
                    new Assert\AtLeastOneOf( [
                        new Assert\Regex("/{$matchYoutube}/"),
                        new Assert\Regex("/{$matchDailyM}/")
                    ],
                        message:"L'URL de la vidéo est invalide, veuillez insérer l'URL d'une vidéo Youtube ou Dailymotion.",
                        includeInternalMessages: false
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
