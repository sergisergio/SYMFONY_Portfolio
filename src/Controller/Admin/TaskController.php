<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller gérant les tâches dans la partie administrateur
 *
 * @package App\Controller\Admin
 *
 * @Route("/admin")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class TaskController extends AbstractController
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
     * Permet d'afficher les tâches
     *
     * @Route("/admin_tasks", name="admin_tasks")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function tasks(Request $request, PaginatorInterface $paginator)
    {
        $tasks = $paginator->paginate(
            $queryBuilder = $this->em->getRepository(Task::class)->findAll(),
            $request->query->getInt('page', 1), 15
        );
        $user = $this->getUser();
        return $this->render('Admin/Task/tasks.html.twig', [
            'pagination' => $tasks,
            'user' => $user
        ]);
    }

    /**
     * Permet d'ajouter ou de modifier une tâche
     *
     * @Route("/admin/task/{id<\d+>}/edit", name="task_edit", methods={"GET", "POST"})
     * @Route("/admin/task/add", name="task_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Task|null $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createOrUpdateTask(Request $request, Task $task = null)
    {
        if (!$task) {
            $task = new Task();
        }

        $form = $this->createForm(TaskType::class, $task);
        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid()) {
            if (!$task->getId()) {
                $task->setCreatedAt(new \DateTime());
                $task->setIsDone(false);
                $task->setUser($this->getUser());
            }
            $this->em->persist($task);
            $this->em->flush();
            $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');

            return $this->redirectToRoute('admin_tasks');
        }

        return $this->render(
            'admin/Task/createOrUpdate.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * Permet de supprimer une tâche
     *
     * @Route("/admin_task_delete/{id}", name="task_delete")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTask($id)
    {
        $repo = $this->em->getRepository(Task::class);
        $task = $repo->find($id);

        $this->em->remove($task);
        $this->em->flush();

        $this->addFlash('message', 'La tâche a bien été supprimé');
        return $this->redirectToRoute('admin_tasks');
    }

    /**
     * Permet de modifier le statut d'une tâche
     *
     * @Route("/admin_task_toggle/{id}", name="task_toggle")
     *
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toggleTask(Task $task)
    {
        if ($task->getUser() === $this->getUser()) {
            if ($task->getIsDone()) {
                $task->setIsDone(false);
                $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
            } else {
                $task->setIsDone(true);
                $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme étant encore à faire.', $task->getTitle()));
            }
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('admin_tasks');
    }
}
