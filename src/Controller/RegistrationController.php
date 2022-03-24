<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        if (!(isset($_GET['person']))) {
            $_GET['person'] = 'student';
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            if (isset($_GET['person']) && $_GET['person'] === 'student') {
                // validate student account
                $user->setIsValidated(true);

                // add role for student
                $user->setRoles(['ROLE_USER', 'ROLE_STUDENT']);
            }

            if (isset($_GET['person']) && $_GET['person'] === 'instructor') {
                // instructor account must be validated by administrator
                $user->setIsValidated(false);

                // add role for student
                $user->setRoles(['ROLE_USER', 'ROLE_INSTRUCTOR']);

                $pictureFile = $form->get('picture')->getData();
                if ($pictureFile) {
                    $pictureFileName = $fileUploader->upload($pictureFile);
                    $user->setPicture($pictureFileName);
                }
            }


            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
