<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 21/07/2019
 * Time: 12:00
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/blog", name="blog")
     */
    public function home()
    {
        $posts = $this->em->getRepository(Posts::class)->findAll();
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('Blog/blog.html.twig', [
            'posts' => $posts,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/blog2", name="blog2")
     */
    public function home2()
    {
        $posts = $this->em->getRepository(Posts::class)->findAll();
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('Blog/blog2.html.twig', [
            'posts' => $posts,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/blog3", name="blog3")
     */
    public function home3()
    {
        $posts = $this->em->getRepository(Posts::class)->findAll();
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $this->render('Blog/blog3.html.twig', [
            'posts' => $posts,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/view", name="view")
     */
    public function view()
    {
        return $this->render('Blog/view.html.twig');
    }
}