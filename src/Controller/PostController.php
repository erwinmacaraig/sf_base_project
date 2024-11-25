<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'posts.index', methods:['GET'])]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/post/new', name:'posts.new', methods:['GET', 'POST'])]
    public function new(): Response
    {
        return $this->render('post/new.html.twig');
    }

    #[Route('/post/{id}', name:'posts.show', methods:['GET'])]
    public function show($id): Response
    {
        return $this->render('post/show.html.twig');
    }

    #[Route('/post/{id}/edit', name:'posts.edit', methods: ['GET', 'POST'])]
    public function edit($id): Response 
    {
        return $this->redirectToRoute('posts.index');
    }

    #[Route('/post/{id}/delete', name:'posts.delete', methods:['GET', 'POST'])]
    public function delete($id): Response 
    {
        return new Response('Delete post from database');
    }

    #[Route('/posts/user/{id}', name:'posts.user', methods:['GET'])]
    public function user($id): Response
    {
        return $this->render('post/index.html.twig');
    }
}
