<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/commentaires/{id}/signaler', name: 'app_comment_report', methods: ['POST'])]
    public function report(Request $request, int $id): Response
    {
        $reason = trim((string) $request->request->get('reason', ''));

        return new JsonResponse([
            'status' => 'reported',
            'comment_id' => $id,
            'reason' => $reason,
        ], Response::HTTP_ACCEPTED);
    }

    #[Route('/post/{slug}/commentaires', name: 'app_comment_create', methods: ['POST'])]
    public function create(Request $request, string $slug): Response
    {
        $content = trim((string) $request->request->get('content', ''));

        return new JsonResponse([
            'status' => 'created',
            'post_slug' => $slug,
            'content_length' => mb_strlen($content),
        ], Response::HTTP_CREATED);
    }
}
