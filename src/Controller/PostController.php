<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class PostController extends AbstractController
{
    // route pour afficher tout les posts
    #[Route('/post', name: 'app_post')]
    public function list(EntityManagerInterface $em): Response
    {
        $posts = $em->getRepository(Post::class)->findBy(['isPublished' => true]);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    // route pour afficher les détails d'un post
    #[Route('/post/{slug}', name: 'app_post_show')]
    public function show(
        string $slug,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug]);

        if (!$post) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        $comments = $em->getRepository(Comment::class)->findBy([
            'post' => $post,
            'isValidated' => true
        ]);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $comment->setIsValidated(false); // à valider par l'admin
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire ajouté, en attente de validation.');
            return $this->redirectToRoute('app_post_show', ['slug' => $slug]);
        }
        if ($request->isMethod('POST') && $request->request->get('report_comment_id')) {
            $commentId = $request->request->get('report_comment_id');
            $commentToReport = $em->getRepository(Comment::class)->find($commentId);
            if ($commentToReport) {
                $commentToReport->setIsReported(true);
                $em->flush();
                $this->addFlash('warning', 'Commentaire signalé.');
                return $this->redirectToRoute('app_post_show', ['slug' => $slug]);
            }
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'comment_form' => $form->createView(),
        ]);
    }
}
