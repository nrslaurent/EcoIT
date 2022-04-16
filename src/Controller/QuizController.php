<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    /**
     * @Route("/quiz", name="app_quiz")
     */
    public function index(QuestionRepository $questionRepository, Request $request): Response
    {
        $courseId = null;
        if (isset($_GET['course'])) {
            $courseId = $_GET['course'];
        }
        $questions = $questionRepository->findBy(array('course' => $courseId), array('id' => 'ASC'));

        //Ajax request for done lesson
        if ($request->isXmlHttpRequest()) {
            $json = [];
            foreach ($questions as $question) {
                array_push($json, array(
                    'id' => $question->getId(),
                    'question' => $question->getQuestion(),
                    'answers' => $question->getAnswers()
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

        return $this->render('quiz/quiz.html.twig', [
            'questions' => $questions,
        ]);
    }
}
