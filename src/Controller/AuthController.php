<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder= $encoder;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils,Request $request): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('quiz');
         }

         $error =null;
         $lastUsername = null;
         $recaptcha = new ReCaptcha($this->getParameter('google_recaptcha_secret'));
         $response = $recaptcha->verify($request->request->get('g-recaptcha-response'),$request->getClientIp());
        // get the login error if there is one
        if (!$response->isSuccess()) {
            $recaptchaError = "Recaptcha error!";
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'google_site_key'=> $this->getParameter('google_recaptcha_site_key')
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);

        $recaptcha = new ReCaptcha($this->getParameter('google_recaptcha_secret'));
        $response = $recaptcha->verify($request->request->get('g-recaptcha-response'),$request->getClientIp());

        if($form->isSubmitted()){
            if($form->get('password')->getData() !=
                $form->get('passwordConfirmation')->getData())
            {
                $form->addError(new FormError('Passwords do not match!'));
            }
            else if($form->get('agreeTerms')->getData()==false)
            {
                $form->addError(new FormError('Please accept terms!'));
            }
            else if($form->isValid() && $response->isSuccess())
            {
                $user = $form->getData();
                $user->setPassword($this->encoder->encodePassword($user,$user->getPassword()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $this->addFlash('registration-success','You account was created! Please login.');
                return $this->redirect($this->generateUrl('app_login'));
            }
        }

        return $this->render('security/registration.html.twig',[
            'form'=>$form->createView(),
            'google_site_key'=>$this->getParameter('google_recaptcha_site_key')
        ]);
    }
}
