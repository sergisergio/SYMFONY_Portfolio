<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 21/07/2019
 * Time: 12:00
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Posts;
use App\Entity\Tag;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller gérant le blog
 *
 * @Route("/blog")
 *
 * @package App\Controller
 */
class BlogController extends AbstractController
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
     * Méthode permettant d'afficher les articles (affichage 1)
     *
     * @Route("/", name="blog")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home(Request $request, PaginatorInterface $paginator)
    {
        $posts = $paginator->paginate(
          $queryBuilder = $this->em->getRepository(Posts::class)->findAll(),
          $request->query->getInt('page', 1), 5
        );
        $tags = $this->em->getRepository(Tag::class)->findAll();
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('Blog/blog.html.twig', [
            'pagination' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'user' => $this->getUser()
        ]);
    }

    /**
     * Méthode permettant d'afficher les articles (affichage 2)
     *
     * @Route("/v2", name="blog2")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home2(Request $request, PaginatorInterface $paginator)
    {
        $posts = $paginator->paginate(
            $queryBuilder = $this->em->getRepository(Posts::class)->findAll(),
            $request->query->getInt('page', 1), 10
        );
        $tags = $this->em->getRepository(Tag::class)->findAll();
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('Blog/blog2.html.twig', [
            'pagination' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'user' => $this->getUser()
        ]);
    }

    /**
     * Méthode permettant d'afficher les articles (affichage 3)
     *
     * @Route("/v3", name="blog3")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home3(Request $request, PaginatorInterface $paginator)
    {
        $posts = $paginator->paginate(
            $queryBuilder = $this->em->getRepository(Posts::class)->findAll(),
            $request->query->getInt('page', 1), 8
        );
        $tags = $this->em->getRepository(Tag::class)->findAll();
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('Blog/blog3.html.twig', [
            'pagination' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'user' => $this->getUser()
        ]);
    }

    /**
     * Méthode permettant d'afficher un article
     *
     * @Route("/{id}/view", name="view")
     *
     * @param Request $request
     * @param Posts $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function view(Request $request, Posts $post)
    {
        $tags = $this->em->getRepository(Tag::class)->findAll();
        $comments = $this->em->getRepository(Comment::class)->findAll();

        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPublishedAt(new \DateTime())
                ->setPost($post)
                ->setValidated(true);

            $this->em->persist($comment);
            $this->em->flush();
            return $this->redirectToRoute('view', ['id' => $post->getId()]);
        }


        return $this->render('Blog/view.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'form' => $form->createView(),
            'tags' => $tags,
            'user' => $this->getUser()
        ]);
    }
}
