<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 14/08/2019
 * Time: 20:08
 */

namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller gérant les profils utilisateurs
 *
 * @Route("/account")
 *
 * @package App\Controller
 */
class AccountController extends AbstractController
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
     * Méthode permettant d'afficher le profil
     *
     * @Route("/user/{id<\d+>}", name="account")
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(User $user)
    {
        return $this->render('Account/account.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Méthode permettant de modifier le profil
     *
     * @Route("/user/{id}/edit", name="edit_account")
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(User $user)
    {
        return $this->render('Account/edit.html.twig', [
            'user' => $user
        ]);
    }
}
