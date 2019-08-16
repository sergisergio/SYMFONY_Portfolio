<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 21/07/2019
 * Time: 13:33
 */

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller permettant d'accéder à la partie administrateur
 *
 * @package App\Controller\Admin
 *
 * @Route("/admin")
 *
 * @IsGranted("ROLE_ADMIN")
 */
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
     * @Route("/", name="admin")
     */
    public function admin()
    {

        return $this->render('Admin/index.html.twig');
    }
}