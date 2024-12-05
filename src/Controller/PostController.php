<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/{_locale?}', name: 'posts.index', methods:['GET'])]
    public function index(): Response
    {
        return $this->render('post/index.html.twig');
    }

    #[Route('/post/new', name:'posts.new', methods:['GET', 'POST'])]
    public function new(): Response
    {
        return $this->render('post/new.html.twig');
    }

    #[Route('/post/{_locale?}/{id}', name:'posts.show', methods:['GET'])]
    public function show($id): Response
    {
        return $this->render('post/show.html.twig');
    }

    #[Route('/post/{id}/edit', name:'posts.edit', methods: ['GET', 'POST'])]
    public function edit($id): Response 
    {
        // return $this->redirectToRoute('posts.index');
        return $this->render('post/edit.html.twig');
    }

    #[Route('/post/{id}/delete', name:'posts.delete', methods:['GET', 'POST'])]
    public function delete($id): Response 
    {
        return new Response('Delete post from database');
    }

      
    #[Route('/posts/user/{id}', methods: ['GET'], name: 'posts.user')]
    public function user($id): Response
    {
        return new Response(
            '<h1>List of posts from specific user <br>' .
            'User id: ' . $id . '<br>' .
            'Named route that we will use in the view: ' .
            $this->generateUrl('posts.user', ['id' => $id]) .
            '</h1>'
        );
    }

    #[Route('/toggleFollow/{user}', methods: ['GET'], name: 'toggleFollow')]
    public function toggleFollow($user): Response
    {
        return new Response(
            '<h1>Toggle like/dislike<br>' .
            'User id: ' . $user . '<br>' .
            'Named route that we will use in the view: ' .
            $this->generateUrl('toggleFollow', ['user' => $user]) .
            '</h1>'
        );
    }
}
