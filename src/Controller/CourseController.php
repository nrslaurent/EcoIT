<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/course")
 */
class CourseController extends AbstractController
{
    /**
     * @Route("/", name="app_course_index", methods={"GET"})
     */
    public function index(CourseRepository $courseRepository): Response
    {
        return $this->render('course/index.html.twig', [
            'courses' => $courseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_course_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CourseRepository $courseRepository): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $courseRepository->add($course);
            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_course_show", methods={"GET"})
     */
    public function show(Course $course): Response
    {
        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_course_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Course $course, CourseRepository $courseRepository): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $courseRepository->add($course);
            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_course_delete", methods={"POST"})
     */
    public function delete(Request $request, Course $course, CourseRepository $courseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
            $courseRepository->remove($course);
        }

        return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/inprogress/{id}", name="app_course_inprogress", methods={"GET"})
     */
    public function inProgress(Request $request, Course $course, CourseRepository $courseRepository, SectionRepository $sectionRepository, LessonRepository $lessonRepository, EntityManagerInterface $entityManager): Response
    {

        if (!isset($_GET['startCourse'])) {
            $_GET['startCourse'] = "false";
        }

        if (!$course->getChosenBy()->contains($this->getUser()->getId())) {
            $course->addChosenBy($this->getUser());
            $entityManager->persist($course);
            $entityManager->flush();
        }

        if (!(isset($_GET['finishedLesson']))) {
            $_GET['finishedLesson'] = "false";
        }

        $firstLesson = $lessonRepository->findFirstLessonBySection($sectionRepository->findFirstSectionByCourse($course->getId()));

        //Ajax request to get lessons list
        if ($request->isXmlHttpRequest()) {
            foreach ($sectionRepository->findAll() as $section) {
                if ($section->getContainedIn()->getTitle() === $course->getTitle() && $section->getTitle() === $_GET['section']) {
                    $lessons = $lessonRepository->findBySection($section->getId());
                    if ($_GET['finishedLesson'] === "true") {
                        foreach ($lessons as $lesson) {
                            if ($lesson->getTitle() === $_GET['lesson']) {
                                $data = $lesson->getFinishedBy();
                                array_push($data, $this->getUser()->getId());
                                $lesson->setFinishedBy($data);
                                $entityManager->persist($lesson);
                                $entityManager->flush();
                            }
                        }
                        $_GET['finishedLesson'] = false;
                    }
                }
            }
            $json = [];

            foreach ($lessons as $lesson) {
                array_push($json, array(
                    'id' => $lesson->getId(),
                    'title' => $lesson->getTitle(),
                    'description' => $lesson->getDescription(),
                    'video' => $lesson->getVideo(),
                    'resources' => $lesson->getResources(),
                    'finishedLesson' => $lesson->getFinishedBy(),
                    'userId' => $this->getUser()->getId()
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

        return $this->renderForm('course/inProgress.html.twig', [
            'course' => $course,
            'lesson' => $firstLesson[0]
        ]);
    }
}
