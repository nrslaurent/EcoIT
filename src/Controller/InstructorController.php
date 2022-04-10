<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstructorController extends AbstractController
{
    /**
     * @Route("/instructor/{id}", name="app_instructor")
     */
    public function allCourses(Request $request, CourseRepository $courseRepository, SectionRepository $sectionRepository, LessonRepository $lessonRepository, EntityManagerInterface $entityManager): Response
    {

        $allCourses = $courseRepository->getAllCoursesByInstructor($this->getUser()->getId());
        $firstSection = $sectionRepository->findFirstSectionByCourse($allCourses[0]->getId());
        $firstLesson = $lessonRepository->findFirstLessonBySection($sectionRepository->findFirstSectionByCourse($allCourses[0]->getId()));


        if (!isset($_GET['course'])) {
            $_GET['course'] = $allCourses[0]->getTitle();
        }

        if (!isset($_GET['section'])) {
            $_GET['section'] = $firstSection[0]->getTitle();
        }

        if (!isset($_GET['lesson'])) {
            $_GET['lesson'] = $firstLesson[0]->getTitle();
        }

        if (!isset($_GET['isCourseChanged'])) {
            $_GET['isCourseChanged'] = "false";
        }

        //Ajax request to get lessons list
        if ($request->isXmlHttpRequest()) {
            $course = null;
            foreach ($allCourses as $element) {
                if ($element->getTitle() === $_GET['course']) {
                    $course = $element;
                }
            }

            $allSections = $sectionRepository->findBy(array('containedIn' => $course), array('id' => 'ASC'));
            $json = [];
            if ($_GET['isCourseChanged'] === "true") {
                $sectionsArray = [];
                foreach ($allSections as $section) {
                    array_push($sectionsArray, array(
                        'section' => $section->getTitle()
                    ));
                }
                $_GET['isCourseChanged'] = "false";
                array_push($json, $sectionsArray);

                $lessonsArray = [];
                foreach ($allSections as $section) {
                    if ($section->getTitle() === $_GET['section']) {
                        $lessons = $lessonRepository->findBySection($section->getId());
                        foreach ($lessons as $lesson) {
                            array_push($lessonsArray, array(
                                'section' => $section->getTitle(),
                                'id' => $lesson->getId(),
                                'title' => $lesson->getTitle(),
                                'description' => $lesson->getDescription(),
                                'video' => $lesson->getVideo(),
                                'resources' => $lesson->getResources(),
                                'userId' => $this->getUser()->getId(),
                                'finishedLesson' => "",
                                'isInstructor' => true
                            ));
                        }
                    }
                }
                array_push($json, $lessonsArray);
            } else {
                $json = [];
                foreach ($allSections as $section) {
                    if ($section->getTitle() === $_GET['section']) {
                        $lessons = $lessonRepository->findBySection($section->getId());
                        foreach ($lessons as $lesson) {
                            array_push($json, array(
                                'section' => $section->getTitle(),
                                'id' => $lesson->getId(),
                                'title' => $lesson->getTitle(),
                                'description' => $lesson->getDescription(),
                                'video' => $lesson->getVideo(),
                                'resources' => $lesson->getResources(),
                                'userId' => $this->getUser()->getId(),
                                'finishedLesson' => "",
                                'isInstructor' => true
                            ));
                        }
                    }
                }
            }

            return new JsonResponse(
                array(
                    'status' => 'OK',
                    'message' => $json
                ),
                200
            );
        }



        return $this->renderForm('instructor/instructor.html.twig', [
            "allCourses" => $allCourses,
            'course' => $allCourses[0],
            'lesson' => $firstLesson[0]
        ]);
    }
}
