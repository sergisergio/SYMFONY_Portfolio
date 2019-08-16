<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 14/08/2019
 * Time: 12:51
 */

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Classe gérant les utiisateurs
 *
 * @package App\Controller\Admin
 *
 * @Route("/admin")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
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
     * Permet d'afficher la liste des utilisateurs
     *
     * @Route("/admin_users", name="admin_users")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function users(Request $request, PaginatorInterface $paginator)
    {
        $users = $paginator->paginate(
            $queryBuilder = $this->em->getRepository(User::class)->findAll(),
            $request->query->getInt('page', 1), 5
        );
        return $this->render('Admin/User/users.html.twig', [
            'pagination' => $users
        ]);
    }
}
