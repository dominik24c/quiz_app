<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Solution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $quizzes = $this->entityManager->getRepository(Quiz::class)
            ->getTheMostPopularQuizzes(3);
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
