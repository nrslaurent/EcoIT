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
use Symfony\Component\Validator\Constraints\Length;

class InstructorController extends AbstractController
{
    /**
     * @Route("/instructor/{id}", name="app_instructor")
     */
    public function allCourses(Request $request, CourseRepository $courseRepository, SectionRepository $sectionRepository, LessonRepository $lessonRepository, EntityManagerInterface $entityManager, $id): Response
    {
        if (count($courseRepository->getAllCoursesByInstructor($id)) > 0 && count($sectionRepository->findby(array('createdBy' => $this->getUser()))) > 0 && count($lessonRepository->findby(array('createdBy' => $this->getUser()))) > 0) {
            $firstCourse = $courseRepository->getAllCoursesByInstructor($id)[0];
            $firstSection = $sectionRepository->findFirstSectionByCourse($firstCourse->getId())[0];
            $firstLesson = $lessonRepository->findFirstLessonBySection($firstSection)[0];
            $currentCourseId = null;
            $currentSectionId = null;
            $currentLessonId = null;

            if (!(isset($_GET['course']))) {
                $currentCourseId = $firstCourse->getId();
            } else {
                $currentCourseId = $_GET['course'];
            }

            if (!isset($_GET['section'])) {
                $currentSectionId = $firstSection->getId();
            } else {
                $currentSectionId = $_GET['section'];
            }

            if (!isset($_GET['lesson'])) {
                $currentLessonId = $firstLesson->getId();
            } else {
                $currentLessonId = $_GET['lesson'];
            }


            $currentCourse = $courseRepository->find($currentCourseId);
            $sections = $sectionRepository->findSectionsByCourse($currentCourseId);
            $lessons = $lessonRepository->findBySection($currentSectionId);
            $currentLesson = $lessonRepository->find($currentLessonId);


            return $this->renderForm('instructor/instructor.html.twig', [
                'courses' => $courseRepository->getAllCoursesByInstructor($id),
                'currentCourse' => $currentCourse,
                'sections' => $sections,
                'lessons' => $lessons,
                'CurrentLesson' => $currentLesson
            ]);
        } else {
            return $this->renderForm('instructor/instructor.html.twig', [
                'courses' => [],
                'currentCourse' => null,
                'sections' => [],
                'lessons' => [],
                'CurrentLesson' => null,
            ]);
        }
    }
}
