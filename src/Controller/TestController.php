<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TestController.php',
        ]);
    }

    #[Route('/sample', name:'sample', methods:['GET'])]
    public function sample(Request $request): Response 
    {
        return $this->render('sample/sample.html.twig');
    }

    #[Route('/test-db-connect', name:'test_connection_db', methods:['GET'])]
    public function testDbConnection(EntityManagerInterface $em): Response | JsonResponse
    {
        try 
        {
            $connected = !$em->getConnection()->isConnected();

            return $this->json([
                'status' => $em->getConnection()->isConnected(),
                'message' => $connected ? 'connected.' : 'failed.'
            ]);
        }
        catch(\Exception $e)
        {   
            return $this->json([
                'status' => false,
                'message' => 'Connect to database failed',
                'error' => $e->getMessage()
            ]);
        }


    }
}
