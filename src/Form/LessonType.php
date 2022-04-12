<?php

namespace App\Form;

use App\Entity\Lesson;
use App\Entity\Section;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class LessonType extends AbstractType
{
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userId = $this->user->getId();
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
                'query_builder' => function (EntityRepository $er) use ($userId) {
                    return $er->createQueryBuilder('e')
                        ->andWhere('e.createdBy = :val')
                        ->setParameter('val', $userId)
                        ->orderBy('e.id', 'ASC');
                },
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
