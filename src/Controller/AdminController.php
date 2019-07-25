<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 21/07/2019
 * Time: 13:33
 */

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminController extends AbstractController
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
     * @Route("/admin", name="admin")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function admin()
    {
        return $this->render('Admin/index.html.twig');
    }

    /**
     * @Route("/admin_posts", name="admin_posts")
     */
    public function posts()
    {
        $posts = $this->em->getRepository(Posts::class)->findAll();
        return $this->render('Admin/posts.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/admin_post_delete/{id}", name="post_delete")
     */
    public function delete($id)
    {
        $repo = $this->em->getRepository(Posts::class);
        $post = $repo->find($id);

        $this->em->remove($post);
        $this->em->flush();

        $this->addFlash('message', 'Le post a bien été supprimé');
        return $this->redirectToRoute('admin_posts');
    }

    /**
     * Permet d'ajouter ou de modifier un post
     *
     * @Route("/admin/post//{id<\d+>}/edit", name="post_edit", methods={"GET", "POST"})
     * @Route("/admin/post/add", name="post_add", methods={"GET", "POST"})$
     *
     * @param Request $request
     * @param Posts $post
     *
     * @return Response
     */
    public function createOrUpdateUser(Request $request, Posts $post = null)
    {
        if (!$post) {
            $post = new Posts();
        }

        $form = $this->createForm(PostType::class, $post);
        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid()) {

            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');

            return $this->redirectToRoute('admin_posts');
        }

        return $this->render(
            'admin/Post/createOrUpdate.html.twig', [
            'form' => $form->createView()
        ]);
    }
}