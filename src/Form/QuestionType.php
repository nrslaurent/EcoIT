<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Question;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class QuestionType extends AbstractType
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
            ->add('course', EntityType::class, [
                'label' => 'Attachée à la formation:',
                'class' => Course::class,
                'query_builder' => function (EntityRepository $er) use ($userId) {
                    return $er->createQueryBuilder('e')
                        ->andWhere('e.createBy = :val')
                        ->setParameter('val', $userId)
                        ->orderBy('e.id', 'ASC');
                },
                'choice_label' => 'title',
            ])
            ->add('question', TextType::class, [
                'label' => 'Question'
            ])
            ->add('answer1', TextType::class, [
                'label'    => 'Réponse 1',
                'mapped' => false,
            ])
            ->add(
                'answer1Checkbox',
                CheckboxType::class,
                [
                    'label' => 'Bonne réponse',
                    'mapped' => false,
                    'required' => false
                ]
            )
            ->add('answer2', TextType::class, [
                'label'    => 'Réponse 2',
                'mapped' => false,
                'required' => false
            ])
            ->add(
                'answer2Checkbox',
                CheckboxType::class,
                [
                    'label' => 'Bonne réponse',
                    'mapped' => false,
                    'required' => false
                ]

            )
            ->add('answer3', TextType::class, [
                'label'    => 'Réponse 3',
                'mapped' => false,
                'required' => false
            ])
            ->add(
                'answer3Checkbox',
                CheckboxType::class,
                [
                    'label' => 'Bonne réponse',
                    'mapped' => false,
                    'required' => false
                ]

            )
            ->add('answer4', TextType::class, [
                'label'    => 'Réponse 4',
                'mapped' => false,
                'required' => false
            ])
            ->add(
                'answer4Checkbox',
                CheckboxType::class,
                [
                    'label' => 'Bonne réponse',
                    'mapped' => false,
                    'required' => false,
                ]

            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
