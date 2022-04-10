<?php

namespace App\Form;

use App\Entity\Lesson;
use App\Entity\Section;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('video', TextType::class, [
                'label' => 'Lien vers la vidéo'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('resources', FileType::class, [
                'label' => 'Ressources',
                'multiple' => true,
                'label_attr' => [
                    'class' => 'old-rose ',
                ],
                'mapped' => false,

                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'application/pdf'
                                ],
                                'mimeTypesMessage' => 'Le fichier doit être au format pdf, jpeg, jpg ou png.',
                            ])
                        ]
                    ])
                ],
            ])
            ->add('containedIn', EntityType::class, [
                'label' => 'Attachée à la section:',
                'class' => Section::class,
                'choice_label' => 'title',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
