<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($_GET['person'] === 'student') {
            $builder
                ->add('email', TextType::class, [
                    'label' => 'Adresse email',
                    'label_attr' => [
                        'class' => 'old-rose ',
                    ],
                    'row_attr' => [
                        'class' => 'mb-4',
                    ],
                ])
                ->add('nickname', TextType::class, [
                    'label' => 'Pseudo',
                    'label_attr' => [
                        'class' => 'old-rose ',
                    ],
                    'row_attr' => [
                        'class' => 'mb-4',
                    ],
                ])
                ->add('plainPassword', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'old-rose ',
                    ],
                    'row_attr' => [
                        'class' => 'mb-4',
                    ],
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new Regex([
                            // password must have a lower case letter, an upper case letter and a number. Minimum length is 6.
                            'pattern' => '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$/',
                            'message' => 'Le mot de passe ne doit pas être inférieur à 6 caractères et il doit contenir au moins une minuscule, une majuscule et un chiffre'
                        ]),
                    ],
                ]);
        } elseif ($_GET['person'] === 'instructor') {
            $builder
                ->add('email', TextType::class, [
                    'label' => 'Adresse email',
                    'label_attr' => [
                        'class' => 'old-rose ',
                    ]
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'Prénom',
                    'label_attr' => [
                        'class' => 'old-rose ',
                    ]
                ])
                ->add('lastname', TextType::class, [
                    'label' => 'Nom',
                    'label_attr' => [
                        'class' => 'old-rose ',
                    ]
                ])
                ->add('picture', FileType::class, [
                    'label' => 'Photo',
                    'label_attr' => [
                        'class' => 'old-rose ',
                    ],
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Le fichier doit être au format jpeg, jpg ou png.',
                        ])
                    ],
                ])
                ->add('skills', TextareaType::class, [
                    'label' => 'Mes spécialités',
                    'label_attr' => [
                        'class' => 'old-rose ',
                    ]
                ])
                ->add('plainPassword', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'old-rose ',
                    ],
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new Regex([
                            // password must have a lower case letter, an upper case letter and a number. Minimum length is 6.
                            'pattern' => '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$/',
                            'message' => 'Le mot de passe ne doit pas être inférieur à 6 caractères et il doit contenir au moins une minuscule, une majuscule et un chiffre'
                        ]),
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
