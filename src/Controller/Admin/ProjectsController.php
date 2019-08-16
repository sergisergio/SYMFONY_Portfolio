<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 13/08/2019
 * Time: 22:45
 */

namespace App\Controller\Admin;

use App\Entity\Projects;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller gérant les projets
 *
 * @Route("/admin")
 *
 * @package App\Controller\Admin
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ProjectsController extends AbstractController
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
     * Permet d'afficher les projets
     *
     * @Route("/projects", name="admin_projects")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function projectsView(Request $request, PaginatorInterface $paginator)
    {
        $projects = $paginator->paginate(
            $queryBuilder = $this->em->getRepository(Projects::class)->findAll(),
            $request->query->getInt('page', 1), 15
        );
        return $this->render('Admin/Project/projects.html.twig', [
            'pagination' => $projects
        ]);
    }

    /**
     * Permet d'ajouter ou de modifier un projet
     *
     * @Route("/project/{id<\d+>}/edit", name="project_edit", methods={"GET", "POST"})
     * @Route("/project/add", name="project_add", methods={"GET", "POST"})$
     *
     * @param Request $request
     * @param Projects $project
     *
     * @return Response
     */
    public function createOrUpdateProject(Request $request, Projects $project = null)
    {
        if (!$project) {
            $project = new Projects();
        }

        $form = $this->createForm(ProjectType::class, $project);
        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid()) {
            if (!$project->getId()) {
                $project->setCreatedAt(new \DateTime());
            }
            $this->em->persist($project);
            $this->em->flush();
            $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');

            return $this->redirectToRoute('admin_projects');
        }

        return $this->render(
            'admin/Project/createOrUpdate.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * Permet de supprimer un projet
     *
     * @Route("/admin_project_delete/{id}", name="project_delete")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProject($id)
    {
        $repo = $this->em->getRepository(Projects::class);
        $project = $repo->find($id);

        $this->em->remove($project);
        $this->em->flush();

        $this->addFlash('message', 'Le projets a bien été supprimé');
        return $this->redirectToRoute('admin_projects');
    }
}
