<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(CourseRepository $courseRepository): Response
    {

        return $this->render('homepage/index.html.twig', [
            'courses' => $courseRepository->GetLastPublishedCourses(),
        ]);
    }
}
