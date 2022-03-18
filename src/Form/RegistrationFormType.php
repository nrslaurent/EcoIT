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

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($_GET['person'] === 'student') {
            $builder
                ->add('email', TextType::class, [
                    'label' => 'Adresse email',
                ])
                ->add('nickname', TextType::class, [
                    'label' => 'Pseudo',
                ])
                ->add('plainPassword', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'label' => 'Mot de passe',
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
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'Prénom',
                ])
                ->add('lastname', TextType::class, [
                    'label' => 'Nom',
                ])
                ->add('picture', FileType::class, [
                    'label' => 'Photo',
                    'mapped' => false,
                ])
                ->add('skills', TextareaType::class, [
                    'label' => 'Mes spécialités',
                ])
                ->add('plainPassword', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'label' => 'Mot de passe',
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
