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
use App\Service\FileUploader;
use DateTimeImmutable;

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
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/new", name="app_course_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CourseRepository $courseRepository, FileUploader $FileUploader): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $course->setCreateBy($this->getUser());
            if ($form->get('isPublished')->getData()) {
                $course->setPublishedAt(new DateTimeImmutable());
            }
            //add picture path
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $pictureFileName = $FileUploader->uploadFile($pictureFile);
                $course->setPicture($pictureFileName);
            }
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
            if ($form->get('isPublished')->getData()) {
                $course->setPublishedAt(new DateTimeImmutable());
            }
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

        $user = $this->getUser();
        $firstSection = $sectionRepository->findFirstSectionByCourse($course->getId())[0];
        $firstLesson = $lessonRepository->findFirstLessonBySection($firstSection)[0];
        $currentSectionId = null;
        $currentLessonId = null;

        if (!(isset($_GET['finishedLesson']))) {
            $_GET['finishedLesson'] = "false";
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

        $sections = $sectionRepository->findSectionsByCourse($course->getId());
        $lessons = $lessonRepository->findBySection($currentSectionId);
        $currentLesson = $lessonRepository->find($currentLessonId);


        if (!$course->getChosenBy()->contains($user->getId())) {
            $course->addChosenBy($user);
            $entityManager->persist($course);
            $entityManager->flush();
        }

        //Ajax request for done lesson
        if ($request->isXmlHttpRequest()) {

            foreach ($sectionRepository->findAll() as $section) {
                if ($section->getContainedIn()->getTitle() === $course->getTitle() && $section->getTitle() === $sectionRepository->find($currentSectionId)->getTitle()) {
                    $lessons = $lessonRepository->findBySection($section->getId());
                    if ($_GET['finishedLesson'] === "true") {
                        foreach ($lessons as $lesson) {
                            if ($lesson->getTitle() === $currentLesson->getTitle()) {

                                $data = $lesson->getFinishedBy();
                                array_push($data, $user->getId());
                                $lesson->setFinishedBy($data);
                                $entityManager->persist($lesson);
                                $entityManager->flush();
                            }
                        }
                        $_GET['finishedLesson'] = false;
                    }
                }
            }

            return new JsonResponse(
                array(
                    'status' => 'OK'
                ),
                200
            );
        }
        return $this->renderForm('course/inProgress.html.twig', [
            'course' => $course,
            'sections' => $sections,
            'lessons' => $lessons,
            'CurrentLesson' => $currentLesson
        ]);
    }
}
