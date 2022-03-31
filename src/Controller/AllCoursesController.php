<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllCoursesController extends AbstractController
{
    /**
     * @Route("/all/courses", name="app_all_courses")
     */
    public function index(CourseRepository $courseRepository, Request $request): Response
    {

        //Ajax request to get result of searchBar
        if ($request->isXmlHttpRequest()) {
            dd($_GET['search']);
            $coursesFind = $courseRepository->searchCourses($_GET['search']);
            $json = [];
            foreach ($coursesFind as $course) {
                array_push($json, array(
                    'title' => $course->getTitle(),
                    'picture' => $course->getPicture(),
                    'description' => $course->getDescription(),
                ));
            }
            return new JsonResponse(
                array(
                    'status' => 'OK',
                    'message' => $json
                ),
                200
            );
        }

        return $this->render('all_courses/allcourses.html.twig', [
            'allCourses' => $courseRepository->getAllCoursesByDate(),
        ]);
    }
}
