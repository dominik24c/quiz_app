<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Quiz;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/user/quizzes")]
class UserQuizzesController extends AbstractController
{
    #[Route('', name: 'user_quizzes')]
    public function index(Request $request): Response
    {
        $pageSize = 5;
        $numOfPage = $request->query->get('page');
        $items = count($this->getUser()->getQuizzes());
        $pages = ceil($items / $pageSize) - 1;

        if($numOfPage >= $pages){
            $numOfPage = $pages;
        }

        if ($request->query->get('create_quiz') == "true"){
            $this->addFlash('success-create-quiz',"Quiz was created!");
        }

        $user = $this->getUser();
        $quizzes =  $this->getDoctrine()->getRepository(Quiz::class)
            ->getQuizzesByUser($user,$pageSize,$pageSize*$numOfPage);

        $urlPrevPage = $this->generateUrl('quizzes',['page'=>$numOfPage-1]);
        $urlNextPage = $this->generateUrl('quizzes',['page'=>$numOfPage+1]);

        return $this->render('user_quizzes/index.html.twig',[
            'quizzes'=>$quizzes,
            'numOfPage'=>$numOfPage,
            'pages'=>$pages,
            'items'=>$items,
            'urlPrevPage'=>$urlPrevPage,
            'urlNextPage'=>$urlNextPage
        ]);
    }

    #[Route('/create',name: 'create_quiz', methods: ['GET'])]
    public function create():Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('user_quizzes/quiz.html.twig',[
            'categories'=>$categories,
            'title'=>"Create Quiz",
            'quiz'=>null,
            'btn_name'=>'Create'
        ]);
    }

    #[Route('/create',name: 'store_quiz',methods: ['POST'])]
    public function store(Request $request, SerializerInterface $serializer, LoggerInterface $logger, ValidatorInterface $validator):Response
    {
        try{
            $quiz = $this->deserializeQuizData($request->getContent(),$this->getUser(),$serializer,$validator,$logger,
            ["quiz","question","answer"]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($quiz);
            $em->flush();
        }catch (\Throwable $exception){
//            $logger->error($exception->getMessage());
            return $this->json(["message"=>"Something went wrong!"],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message'=>"Quiz was created!"]);
    }

    #[Route('/{quiz}/edit',name:'edit_quiz',methods: ["GET"])]
    public function edit(Quiz $quiz):Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $this->checkQuizOwner($quiz,$this->getUser(),'You can edit only own quiz!');

        return $this->render('user_quizzes/quiz.html.twig',[
            'quiz'=>$quiz,
            'categories'=>$categories,
            'title'=>'Edit Quiz',
            'btn_name'=>'Update'
        ]);
    }

    #[Route('/{quiz}/edit/quiz-data',name:'get_quiz_data',methods: ["GET"])]
    public function getQuiz(Quiz $quiz,SerializerInterface $serializer):Response
    {
        $quizArr = $serializer->normalize($quiz,'json',[
            ObjectNormalizer::GROUPS=>["quiz","question","answer","edit_quiz"]
        ]);
        $quizArr["category"]= $quiz->getCategory()->getName();

        return $this->json($quizArr);
    }

    #[Route('/{quiz}/edit',name:'update_quiz',methods: ["POST"])]
    public function  update(Quiz $quiz, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, LoggerInterface $logger):Response
    {
        try{
            $this->checkQuizOwner($quiz,$this->getUser(),'You can edit only own quiz!');
            $this->getDoctrine()->getManager()->initializeObject($quiz);
            $updatedQuiz = $this->deserializeQuizData($request->getContent(),$this->getUser(),$serializer,$validator,$logger,[
                "quiz","question","answer","edit-quiz"
            ]);

            $em = $this->getDoctrine()->getManager();
            $em->persist($updatedQuiz);
            $em->flush();
        }catch (\Throwable $exception){
            $logger->error($exception->getMessage());
            return $this->json(["message"=>"Something went wrong!"],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message'=>"Quiz was updated!",'id'=>$quiz->getId()]);

    }

    #[Route("/{quiz}/delete", name: 'delete_quiz')]
    public function delete(Quiz $quiz)
    {
        if($quiz->getUser()->getId() != $this->getUser()->getId()){
            throw new UnauthorizedHttpException('You can delete only own quiz!');
        }

        $title = $quiz->getTitle();
        $em = $this->getDoctrine()->getManager();
        $em->remove($quiz);
        $em->flush();

        $this->addFlash("success-delete-quiz","Quiz $title was deleted!" );
        return $this->redirectToRoute('user_quizzes');
    }

    public function checkQuizOwner(Quiz $quiz,UserInterface $user, string $message): ?Response
    {
        if($quiz->getUser()->getId() != $user->getId()){
            return new JsonResponse(['message'=>$message],JsonResponse::HTTP_FORBIDDEN);
        }
        return null;
    }

    public function deserializeQuizData(string $data, UserInterface $user,
                                        SerializerInterface $serializer,
                                        ValidatorInterface $validator,
                                        LoggerInterface $logger,
                                        array $groups,
                                        array $context=[]):JsonResponse| Quiz
    {

        $objectNormalizer = [ObjectNormalizer::GROUPS=>$groups];
        $context = array_merge($objectNormalizer,$context);
        $quiz = $serializer->deserialize($data,Quiz::class,'json',$context);
        $categoryName = json_decode($data,true)["category"];
        $quiz->setCategory($this->getDoctrine()->getRepository(Category::class)->findOneBy(['name'=>$categoryName]));
        $quiz->setUser($user);

        $errors = $validator->validate($quiz);
        $logger->info("ERRORS OF VALIDATION QUIZ: ".count($errors)." ".(string)$errors);
        if(count($errors)>0){
            return $this->json(["message"=>"Invalid data!"],JsonResponse::HTTP_BAD_REQUEST);
        }

        return $quiz;
    }
}
