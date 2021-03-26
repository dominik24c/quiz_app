<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Solution;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Quiz::class);
        $quizzes = $repo->getTheMostPopularQuizzes(3);
        //dd($quizzes);
        return $this->render('home/index.html.twig',[
            'quizzes'=> $quizzes
        ]);
    }

    #[Route('/contact',name:'contact')]
    public function contact():Response
    {
        return  $this->render('home/contact.html.twig');
    }
}
