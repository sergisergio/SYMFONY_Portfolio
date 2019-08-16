<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 13/08/2019
 * Time: 23:24
 */

namespace App\Controller\Admin;

use App\Entity\Skill;
use App\Form\SkillType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller gérant les compétences
 * @package App\Controller\Admin
 *
 * @Route("/admin")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class SkillsController extends AbstractController
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
     * Permet d'afficher les compétences
     *
     * @Route("/skills", name="admin_skills")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function skillsView(Request $request, PaginatorInterface $paginator)
    {
        $skills = $paginator->paginate(
            $queryBuilder = $this->em->getRepository(Skill::class)->findAll(),
            $request->query->getInt('page', 1), 15
        );        return $this->render('Admin/Skill/skills.html.twig', [
            'pagination' => $skills
        ]);
    }

    /**
     * Permet d'ajouter ou de modifier une compétence
     *
     * @Route("/skill/{id<\d+>}/edit", name="skill_edit", methods={"GET", "POST"})
     * @Route("/skill/add", name="skill_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Skill|null $skill
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createOrUpdatePost(Request $request, Skill $skill = null)
    {
        if (!$skill) {
            $skill = new Skill();
        }

        $form = $this->createForm(SkillType::class, $skill);
        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid()) {
            if (!$skill->getId()) {
                //$skill->setCreatedAt(new \DateTime());
            }
            $this->em->persist($skill);
            $this->em->flush();
            $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');

            return $this->redirectToRoute('admin_skills');
        }

        return $this->render(
            'admin/Skill/createOrUpdate.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * Permet de supprimer une compétence
     *
     * @Route("/admin_skill_delete/{id}", name="skill_delete")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteSkill($id)
    {
        $repo = $this->em->getRepository(Skill::class);
        $skill = $repo->find($id);

        $this->em->remove($skill);
        $this->em->flush();

        $this->addFlash('message', 'La tâche a bien été supprimée');
        return $this->redirectToRoute('admin_skills');
    }
}
