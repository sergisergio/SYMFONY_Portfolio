<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 21/07/2019
 * Time: 13:44
 */

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\ForgottenPasswordType;
use App\Form\PasswordResetType;
use App\Form\PasswordUpdateType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * Permet de s'inscrire
     *
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

            $token = $generator->generateToken();
            $hash = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($hash);
            $user->setToken($token);
            $user->setRoles(["ROLE_USER"]);
            $user->setActive(false);

            $manager->persist($user);
            $manager->flush();

            $message = (new \Swift_Message('Inscription'))
                ->setFrom('noreply@philippetraon.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('Email/confirm.html.twig', [
                    'user' => $user
                ]), 'text/html');

            $mailer->send($message);

            $this->addFlash(
                'info',
                'Un mail de confirmation vous a été envoyé, cliquez sur le lien pour activer votre compte.'
            );

            return $this->redirectToRoute('login');
        }
        return $this->render('User/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de valider l'inscription dans le mail reçu
     *
     * @Route("/confirmation/{email}/{token}", name="confirmation")
     */
    public function confirm($email, $token, ObjectManager $manager)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($email);

        if (!$token) {
            return new Response(new InvalidCsrfTokenException());
        }


        if ($token != null & $token === $user->getToken()) {
            $user->setActive(true);
            $user->setToken(null);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a été activé avec succès ! Vous pouvez désormais vous connecter !"
            );
        }
        else {
            $this->addFlash(
                'danger',
                "La validation de votre compte a échoué. Le lien de validation a expiré !"
            );
        }

        return $this->redirectToRoute('login');
    }

    /**
     * Permet d'envoyer un lien afin de réinitialiser le mot de passe si celui-ci est oublié
     *
     * @Route("/forgot", name="forgot")
     */
    public function forgot(Request $request,
                          ObjectManager $manager,
                          UserRepository $repo,
                          \Swift_Mailer $mailer,
                          TokenGeneratorInterface $generator)
    {
        $form = $this->createForm(ForgottenPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $form->getData();
            $user = $repo->findOneByEmail($email->getEmail());

            if ($user !== null)
            {
                $token = $generator->generateToken();
                $user->setToken($token);
                $manager->persist($user);
                $manager->flush();

                $message = (new \Swift_Message('Réinitialisation du mot de passe'))
                    ->setFrom('noreply@philippetraon.com')
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('Email/reset.html.twig', [
                        'user' => $user
                    ]), 'text/html');

                $mailer->send($message);

                $this->addFlash(
                    'info',
                    'Un mail de réinitilisation de mot de passe vous a été envoyé, cliquez sur le lien pour le réinitialiser.'
                );

            }
            return $this->redirectToRoute('login');

        }
        return $this->render(
            'User/forgot.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * Permet de réinitialiser le mot de passe
     *
     * @Route("/reset/{email}/{token}", name="reset")
     */
    public function reset(Request $request,
                          UserPasswordEncoderInterface $encoder,
                          ObjectManager $manager,
                          $email,
                          $token
    )
    {
        $token = $request->get('token');
        if (!$token) {
            return new Response(new InvalidCsrfTokenException());
        }
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($email);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PasswordResetType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($user->getToken() === $token)
            {
                $password = $form->getData();
                $hash = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($hash);
                $user->setToken(null);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Mot de passe modifié avec succès !"
                );
                return $this->redirectToRoute('login');
            }
        } else {
            $this->addFlash(
                'danger',
                "La modification du mot de passe a échoué ! Le lien de validation a expiré !"
            );
        }

        return $this->render('User/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de se connecter
     *
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
     * Permet de se déconnecter
     *
     * @Route("/logout", name="logout")
     */
    public function logout() {}
}