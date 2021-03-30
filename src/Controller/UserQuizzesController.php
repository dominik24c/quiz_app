<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Quiz;
use App\Entity\User;
use App\Form\QuizType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/quizzes")
 */
class UserQuizzesController extends AbstractController
{
    #[Route('', name: 'user_quizzes')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('user_quizzes/index.html.twig',[
            'quizzes'=>$user->getQuizzes()
        ]);
    }

    #[Route('/create',name: 'create_quiz')]
    public function create()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('user_quizzes/create.html.twig',[
            'categories'=>$categories
        ]);
    }

    #[Route('/create',name: 'store_quiz',methods: ['POST'])]
    public function create_quiz(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );


        return $this->json([
            'message'=>'Quiz was created!'
        ]);
    }

    #[Route('/{quiz}/edit',name:'edit_quiz')]
    public function edit()
    {
        return $this->render('user_quizzes/index.html.twig');
    }
}
