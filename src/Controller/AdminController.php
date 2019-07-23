<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 21/07/2019
 * Time: 13:33
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        return $this->render('Admin/blog.html.twig');
    }
}