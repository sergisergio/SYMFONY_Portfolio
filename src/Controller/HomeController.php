<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Projects;
use App\Entity\Skill;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/", name="home")
     */
    public function home()
    {
        $skills = $this->em->getRepository(Skill::class)->findAll();
        $projects = $this->em->getRepository(Projects::class)->findAll();
        $categories = $this->em->getRepository(Category::class)->findAll();
        return $this->render('Home/index.html.twig', [
            'projects' => $projects,
            'skills' => $skills,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/{id}/project", name="project")
     */
    public function project($id)
    {
        $project = $this->em->getRepository(Projects::class)->find($id);
        //dd($project);

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
     * @Route("more", name="more")
     */
    public function more()
    {
        $projects = $this->em->getRepository(Projects::class)->findAll();
        dd($projects);

        return $this->render('Home/more.html.twig', [
            'projects' => $projects
        ]);
    }
}