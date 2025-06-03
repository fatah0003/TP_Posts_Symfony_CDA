<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class PostsController extends AbstractController
{
    #[Route('/posts', name: 'post_index')]
    public function index(SessionInterface $session): Response
    {
        if (!$session->has('posts')) {
            $session->set('posts', []);
        }

        $posts = $session->get('posts');

        return $this->render('posts/index.html.twig', [
            'title' => 'Liste des articles',
            'posts' => $posts,
        ]);
    }

    #[Route('/posts/new', name: 'post_new')]
    public function new(Request $request, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $posts = $session->get('posts', []);

            $nextId = $session->get('next_id', 1);
            $session->set('next_id', $nextId + 1);

            $id = $nextId;
            $posts[$id] = [
                'id' => $id,
                'title' => $request->request->get('title'),
                'content' => $request->request->get('content'),
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];

            $session->set('posts', $posts);

            return $this->redirectToRoute('post_index');
        }

        return $this->render('posts/new.html.twig', [
            'title' => 'Créer un article',
        ]);
    }

    #[Route('/posts/details', name: 'post_details')]
    public function details(Request $request): Response
    {
        return $this->render('posts/details.html.twig', [
            'title' => 'Détails d\'un article',
            'title_post' => $request->query->get('title'),
            'content' => $request->query->get('content'),
            'created_at' => $request->query->get('created_at'),
        ]);
    }

    #[Route('/posts/edit/{id}', name: 'post_edit')]
    public function edit(string $id, Request $request, SessionInterface $session): Response
    {
        $posts = $session->get('posts', []);

        if (!isset($posts[$id])) {
            throw $this->createNotFoundException('Article introuvable');
        }

        if ($request->isMethod('POST')) {
            $posts[$id]['title'] = $request->request->get('title');
            $posts[$id]['content'] = $request->request->get('content');
            $session->set('posts', $posts);

            return $this->redirectToRoute('post_index');
        }

        return $this->render('posts/edit.html.twig', [
            'title' => 'Modifier l\'article',
            'post' => $posts[$id],
        ]);
    }

    #[Route('/posts/delete/{id}', name: 'post_delete')]
    public function delete(string $id, SessionInterface $session): Response
    {
        $posts = $session->get('posts', []);

        if (isset($posts[$id])) {
            unset($posts[$id]);
            $session->set('posts', $posts);
        }

        return $this->redirectToRoute('post_index');
    }


}
