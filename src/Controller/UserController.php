<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 21/07/2019
 * Time: 13:44
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/signup", name="signup")
     */
    public function signup(Request $request,
                           ObjectManager $manager,
                           UserPasswordEncoderInterface $encoder,
                           \Swift_Mailer $mailer,
                           TokenGeneratorInterface $generator
    )
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            dd('OK');
            return $this->redirectToRoute('login');
        }
        return $this->render('User/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            return $this->redirect($this->generateUrl('home'));
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $error = ($error instanceof CustomUserMessageAuthenticationException) ? $error->getMessage() : 'lg.authentication.credentials';
        }
        return $this->render('User/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {}
}