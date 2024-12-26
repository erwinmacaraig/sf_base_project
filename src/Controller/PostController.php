<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
// these two are used for database operations 
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/', requirements:['_locale' => 'en|ph'])]
class PostController extends AbstractController
{
    #[Route('/{_locale}', name: 'posts.index', methods:['GET'])]
    public function index(string $_locale='en', PostRepository $postRepository): Response
    {
        // $posts = $postRepository->findBy(['user' => $this->getUser()]);
        // dump($posts);
        return $this->render('post/index.html.twig');
    }

    #[Route('/{_locale}/post/new', name:'posts.new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();        
        $post->setUser($this->getUser());
        $post->setCreatedAt(new \DateTimeImmutable('now'));
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            $entityManagerInterface->persist($post);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('posts.index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{_locale}/post//{id}', name:'posts.show', methods:['GET'])]
    public function show($id): Response
    {
        return $this->render('post/show.html.twig');
    }

    #[Route('/{_locale}/post/{id}/edit', name:'posts.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $post = $form->getData();
            return $this->redirectToRoute('posts.index');
        }
        
        // return $this->redirectToRoute('posts.index');
        return $this->render('post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{_locale}/post/{id}/delete', name:'posts.delete', methods:['GET', 'POST'])]
    public function delete($id): Response 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return new Response('Delete post from database');
    }

      
    #[Route('/{_locale}/posts/user/{id}', methods: ['GET'], name: 'posts.user')]
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

    #[Route('/{_locale}/toggleFollow/{user}', methods: ['GET'], name: 'toggleFollow')]
    public function toggleFollow($user): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return new Response(
            '<h1>Toggle like/dislike<br>' .
            'User id: ' . $user . '<br>' .
            'Named route that we will use in the view: ' .
            $this->generateUrl('toggleFollow', ['user' => $user]) .
            '</h1>'
        );
    }
}
