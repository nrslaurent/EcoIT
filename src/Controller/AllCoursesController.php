<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllCoursesController extends AbstractController
{
    /**
     * @Route("/all/courses", name="app_all_courses")
     */
    public function index(CourseRepository $courseRepository): Response
    {
        return $this->render('all_courses/allcourses.html.twig', [
            'allCourses' => $courseRepository->getAllCoursesByDate(),
        ]);
    }
}
