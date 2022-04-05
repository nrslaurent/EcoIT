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

        if (!isset($_GET['search'])) {
            $_GET['search'] = null;
        }

        if (!isset($_GET['myCourses'])) {
            $_GET['myCourses'] = 'false';
        }

        if (!isset($_GET['doneCourses'])) {
            $_GET['doneCourses'] = 'false';
        }

        if (!isset($_GET['allCourses'])) {
            $_GET['allCourses'] = 'false';
        }


        //Ajax request to get result of searchBar
        if ($request->isXmlHttpRequest()) {
            $json = [];
            if ($_GET['search'] !== null) {
                $json = [];
                $coursesFind = $courseRepository->searchCourses($_GET['search']);
                foreach ($coursesFind as $course) {
                    array_push($json, array(
                        'id' => $course->getId(),
                        'title' => $course->getTitle(),
                        'picture' => $course->getPicture(),
                        'description' => $course->getDescription(),
                    ));
                }
            }

            if ($_GET['myCourses'] === 'true') {
                $json = [];
                $studentCourses = $courseRepository->getAllCoursesByStudent($this->getUser()->getId());
                foreach ($studentCourses as $course) {
                    array_push($json, array(
                        'id' => $course->getId(),
                        'title' => $course->getTitle(),
                        'picture' => $course->getPicture(),
                        'description' => $course->getDescription(),
                    ));
                }
                $_GET['myCourses'] = "false";
            }

            if ($_GET['allCourses'] === 'true') {
                $json = [];
                $allCourses = $courseRepository->getAllCoursesByDate();
                foreach ($allCourses as $course) {
                    array_push($json, array(
                        'id' => $course->getId(),
                        'title' => $course->getTitle(),
                        'picture' => $course->getPicture(),
                        'description' => $course->getDescription(),
                    ));
                }
                $_GET['allCourses'] = 'false';
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
