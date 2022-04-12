<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Section;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SectionType extends AbstractType
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
            ->add('containedIn', EntityType::class, [
                'label' => 'Attachée à la formation:',
                'class' => Course::class,
                'query_builder' => function (EntityRepository $er) use ($userId) {
                    return $er->createQueryBuilder('e')
                        ->andWhere('e.createBy = :val')
                        ->setParameter('val', $userId)
                        ->orderBy('e.id', 'ASC');
                },
                'choice_label' => 'title',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }

    public function getCoursesByInstructor(EntityRepository $er)
    {
        $queryBuilder = $er->createQueryBuilde('e')
            ->andWhere('s.createdBy = :val')
            ->setParameter('val', $this->getUser())
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $queryBuilder;
    }
}
