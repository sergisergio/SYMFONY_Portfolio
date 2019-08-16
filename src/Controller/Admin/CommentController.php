<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 14/08/2019
 * Time: 12:50
 */

namespace App\Controller\Admin;

use App\Entity\Comment;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller permettant de gérer les commentaires
 *
 * @package App\Controller\Admin
 *
 * @Route("/admin")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class CommentController extends AbstractController
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
     * Méthode permettant d'afficher la liste des commentaires
     *
     * @Route("/admin_comments", name="admin_comments")
     */
    public function comments(Request $request, PaginatorInterface $paginator)
    {
        $comments = $paginator->paginate(
            $queryBuilder = $this->em->getRepository(Comment::class)->findAll(),
            $request->query->getInt('page', 1), 5
        );
        return $this->render('Admin/Comment/comments.html.twig', [
            'pagination' => $comments
        ]);
    }

    /**
     * Méthode permettant de supprimer un commentaire
     *
     * @Route("/admin_comment_delete/{id}", name="comment_delete")
     *
     * @param Comment $comment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteComment(Comment $comment)
    {
        $this->em->remove($comment);
        $this->em->flush();

        $this->addFlash('message', 'Le commentaire a bien été supprimé');
        return $this->redirectToRoute('admin_comments');
    }

    /**
     * Méthode permettant de valider ou de ne pas valider un commentaire
     *
     * @Route("/admin_comment_toggle/{id}", name="comment_toggle")
     *
     * @param Comment $comment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validateUnvalidateComment(Comment $comment)
    {
        $comment->getValidated(true) ? $comment->setValidated(false) : $comment->setValidated(true);
        $this->em->flush();

        $this->addFlash('message', 'Validation modifiée');
        return $this->redirectToRoute('admin_comments');
    }
}
