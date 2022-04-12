<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Service\ResourcesUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lesson")
 */
class LessonController extends AbstractController
{
    /**
     * @Route("/", name="app_lesson_index", methods={"GET"})
     */
    public function index(LessonRepository $lessonRepository): Response
    {
        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessonRepository->findby(array('createdBy' => $this->getUser())),
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/new", name="app_lesson_new", methods={"GET", "POST"})
     */
    public function new(Request $request, LessonRepository $lessonRepository, ResourcesUploader $ResourcesUploader): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lesson->setCreatedBy($this->getUser());
            //add resources path
            $resourcesFile = $form->get('resources')->getData();
            $resourcesFilePath = [];
            if ($resourcesFile) {
                foreach ($resourcesFile as $resource) {
                    array_push($resourcesFilePath, $ResourcesUploader->uploadFile($resource));
                }
                $lesson->setResources($resourcesFilePath);
            }
            $lessonRepository->add($lesson);
            return $this->redirectToRoute('app_lesson_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_lesson_show", methods={"GET"})
     */
    /*public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }*/

    /**
     * @Route("/{id}/edit", name="app_lesson_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Lesson $lesson, LessonRepository $lessonRepository, CourseRepository $courseRepository, ResourcesUploader $ResourcesUploader): Response
    {
        if ($lesson->getCreatedBy() === $this->getUser()) {
            $form = $this->createForm(LessonType::class, $lesson);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //add resources path
                $resourcesFile = $form->get('resources')->getData();
                $resourcesFilePath = [];
                if ($resourcesFile) {
                    foreach ($resourcesFile as $resource) {
                        array_push($resourcesFilePath, $ResourcesUploader->uploadFile($resource));
                    }
                    $lesson->setResources($resourcesFilePath);
                }
                $lessonRepository->add($lesson);
                return $this->redirectToRoute('app_lesson_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('lesson/edit.html.twig', [
                'lesson' => $lesson,
                'form' => $form,
            ]);
        } else {
            return $this->render('homepage/index.html.twig', [
                'courses' => $courseRepository->getLastPublishedCourses(),
                'student' => $this->getUser()
            ]);
        }
    }

    /**
     * @Route("/{id}", name="app_lesson_delete", methods={"POST"})
     */
    public function delete(Request $request, Lesson $lesson, LessonRepository $lessonRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $lesson->getId(), $request->request->get('_token'))) {
            $lessonRepository->remove($lesson);
        }

        return $this->redirectToRoute('app_lesson_index', [], Response::HTTP_SEE_OTHER);
    }
}
