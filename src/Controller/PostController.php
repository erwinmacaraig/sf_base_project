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
// use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/', requirements:['_locale' => 'en|ph'])]
class PostController extends AbstractController
{
    #[Route('/{_locale}', name: 'posts.index', methods:['GET'])]
    public function index(Request $request, string $_locale='en', PostRepository $postRepository): Response
    {
        // $posts = $this->getUser()->getPosts();
        // foreach($posts as $post) {
        //     dump($post->getTitle());
        // }
        $posts = $postRepository->findAllPosts($request->query->getInt('page', 1));
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
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
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/{_locale}/post/{id}/edit', name:'posts.edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, EntityManagerInterface $em, Request $request): Response 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $post = $form->getData();
            $post->setUpdatedAt(new \DateTimeImmutable('now'));
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('posts.index');
        }
        
        // return $this->redirectToRoute('posts.index');
        return $this->render('post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{_locale}/post/{id}/delete', name:'posts.delete', methods:['GET', 'POST'])]
    public function delete(Post $post, ManagerRegistry $doctrine): Response 
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($post);
        $entityManager->flush();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->redirectToRoute('posts.index');
    }

      
    #[Route('/{_locale}/posts/user/{id}', methods: ['GET'], name: 'posts.user')]
    public function user(Request $request, PostRepository $repository, $id): Response
    {
        $posts = $repository->findAllUserPosts($request->query->getInt('page', 1), $id);
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
        
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
