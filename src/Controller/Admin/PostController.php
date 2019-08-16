<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 14/08/2019
 * Time: 12:25
 */

namespace App\Controller\Admin;

use App\Entity\Posts;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller gérant les articles du blog
 *
 * @package App\Controller\Admin
 *
 * @Route("/admin")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class PostController extends AbstractController
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
     * Permet d'afficher tous les articles
     *
     * @Route("/admin_posts", name="admin_posts")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function posts(Request $request, PaginatorInterface $paginator)
    {
        $posts = $paginator->paginate(
            $queryBuilder = $this->em->getRepository(Posts::class)->findAll(),
            $request->query->getInt('page', 1), 5
        );
        return $this->render('Admin/Post/posts.html.twig', [
            'pagination' => $posts
        ]);
    }

    /**
     * Permet de supprimer un article
     *
     * @Route("/admin_post_delete/{id}", name="post_delete")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePost($id)
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
     * @Route("/post/{id<\d+>}/edit", name="post_edit", methods={"GET", "POST"})
     * @Route("/post/add", name="post_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Posts $post
     *
     * @return Response
     */
    public function createOrUpdatePost(Request $request, Posts $post = null)
    {
        if (!$post) {
            $post = new Posts();
        }

        $form = $this->createForm(PostType::class, $post);
        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid()) {
            if (!$post->getId()) {
                $post->setCreatedAt(new \DateTime());
            }
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');

            return $this->redirectToRoute('admin_posts');
        }

        return $this->render(
            'admin/Post/createOrUpdate.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
