<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiController extends AbstractController
{
    #[Route('/api/api', name: 'app_api_api')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Api/ApiController.php',
        ]);
    }

    #[Route('/api/post/new', methods:['POST'])]
    public function post(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data || !$data['title'] || !$data['content']) 
            {
                throw new \Exception("data not valid");
            }
            $post = new Post();
            $entityManager->persist($post); 
            $post->setTitle($data['title']);
            $post->setContent($data['content']);
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable('now'));
            $entityManager->flush();
            return $this->json([
                'message' => 'Post added',

            ], 200);
        } catch(\Exception $e) {
            return $this->json([
                'error' => 'Post not added',
                'message' => $e->getMessage()
            ], 400);
        }

    }

    #[Route('/api/register', methods:["POST"])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data || !$data['name'] || !$data['email'] || !$data['password']) 
            {
                throw new Exception("data not valid");                
            }
            $user = new User();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['password']
            );
            $user->setName($data['name']);
            $user->setEmail($data['email']);
            $user->setPassword($hashedPassword);
            $entityManager->persist($user); 
            $entityManager->flush();
            return $this->json([
                'message' => 'User Registered'
            ],200);
        } catch(\Exception $e) {
            return $this->json([
                'error' => "User Not Registered",
                'message' => $e->getMessage()
            ],400);
        }
    }
}
