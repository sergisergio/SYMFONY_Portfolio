<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Projects;
use App\Entity\Skill;
use App\Entity\User;
use App\Form\ContactType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller gérant la page d'accueil
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }

    /**
     * Permet d'afficher la page d'accueil
     *
     * @Route("/", name="home")
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function home(Request $request, \Swift_Mailer $mailer)
    {

        $projects = $this->em->getRepository(Projects::class)->findAll();
        $categories = $this->em->getRepository(Category::class)->findAll();
        $user = $this->em->getRepository(User::class)->findOneByUsername(['username' => 'philippe']);

        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FormData = $form->getData();
            $ip = "Adresse IP: ".$_SERVER["REMOTE_ADDR"];
            $message = (new \Swift_Message('You Got Mail!'))
                ->setFrom($FormData['from'])
                ->setTo('ptraon@gmail.com')
                ->setBody(
                    $this->renderView('Email/contact.html.twig', [
                    'auteur' => $FormData['from'],
                    'message' => $FormData['message'],
                    'ip' => $ip
                    ]),
                    'text/html'
                )
            ;

            $this->addFlash(
                'success',
                "succès"
            );

            $mailer->send($message);

            return $this->redirectToRoute('home');
        }

        return $this->render('Home/index.html.twig', [
            'projects' => $projects,
            //'skills' => $skills,
            'categories' => $categories,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher un projet en particulier
     *
     * @Route("/{id}/project", name="project")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function project($id)
    {
        $project = $this->em->getRepository(Projects::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException(
                'No project found for id '.$id
            );
        }

        return $this->render('Home/project.html.twig', [
            'project' => $project
        ]);
    }

    /**
     * Permet de dérouler la liste des projets
     *
     * @Route("more", name="more")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function more()
    {
        $projects = $this->em->getRepository(Projects::class)->findAll();
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('Home/more.html.twig', [
            'projects' => $projects,
            'categories' => $categories
        ]);
    }
}
