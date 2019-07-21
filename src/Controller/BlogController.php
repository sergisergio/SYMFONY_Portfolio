<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 21/07/2019
 * Time: 12:00
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function home()
    {
        return $this->render('Blog/index.html.twig');
    }

    /**
     * @Route("/view", name="view")
     */
    public function view()
    {
        return $this->render('Blog/view.html.twig');
    }
}