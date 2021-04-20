<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Quiz;
use App\Entity\Solution;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/quiz')]
class QuizController extends AbstractController
{
    #[Route('', name: 'quizzes')]
    public function index(Request $request, SessionInterface $session): Response
    {
        $session->start();
        $solutionMsg = $session->get('solution');
        $session->clear();

        $numOfPage = $request->query->get('page');
        $searchedTitle = $request->query->get('search');

        $quizzes = $this->getDoctrine()->getRepository(Quiz::class)
            ->getQuizzesByTitle(false, title:$searchedTitle);

        $pageSize = 5;
        $items = count($quizzes);
        $pages = ceil($items / $pageSize) - 1;

        if($numOfPage == null){
            $numOfPage = 0;
        }
        else if($numOfPage >= $pages){
            $numOfPage = $pages;
        }


        $quizzes =  $this->getDoctrine()->getRepository(Quiz::class)
            ->getQuizzesByTitle(true,$pageSize,$pageSize*$numOfPage,$searchedTitle);

        $urlPrevPage = $this->generateUrl('quizzes',['page'=>$numOfPage-1, 'search'=>$searchedTitle]);
        $urlNextPage = $this->generateUrl('quizzes',['page'=>$numOfPage+1, 'search'=>$searchedTitle]);

        return $this->render('quiz/index.html.twig',[
            'solutionMsg'=> $solutionMsg,
            'quizzes'=>$quizzes,
            'numOfPage'=>$numOfPage,
            'pages'=>$pages,
            'items'=>$items,
            'urlPrevPage'=>$urlPrevPage,
            'urlNextPage'=>$urlNextPage
        ]);
    }

    #[Route('/{quiz}/solve', name: 'solve_quiz', methods: ['GET'])]
    public function solveQuiz(Quiz $quiz): Response
    {
        return $this->render('quiz/solve_quiz.html.twig',[
            'quiz'=>$quiz
        ]);
    }

    #[Route('/{quiz}/solve', name: 'user_solution', methods: ['POST'])]
    public function saveUserSolution(Quiz $quiz, Request $request,LoggerInterface $logger,
    Session $session): JsonResponse
    {
        try{
            $answersId = json_decode($request->getContent());
            if($answersId ==null){
                throw new \JsonException("Cannot parse json data");
            }
            $solution = new Solution();
            $solution->setQuiz($quiz);
            $solution->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();

            foreach ($answersId as $a){
                $answer = $em->getRepository(Answer::class)->find($a->id);
                $solution->addAnswer($answer);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($solution);
            $em->flush();

        }catch (\Throwable $exception){
            $logger->error($exception->getMessage());
            return $this->json(['message'=>'Cannot create solution'],400);
        }

        $session->start();
        $session->set('solution', 'solutions was saved');

        return $this->json(['message'=>'Solution was added!']);
    }

    #[Route('/{quiz}/get-questions', name: 'get_questions')]
    public function getQuestions(Quiz $quiz, SerializerInterface $serializer): JsonResponse
    {
        $questions = $quiz->getQuestions();
        $data = $serializer->normalize($questions,'json',[
            ObjectNormalizer::GROUPS=>["question","answer","edit_quiz"],
            ObjectNormalizer::IGNORED_ATTRIBUTES=>["isCorrect","points"]
        ]);
        return $this->json($data);
    }
}
