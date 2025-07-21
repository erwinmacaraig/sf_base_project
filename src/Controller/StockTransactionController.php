<?php

namespace App\Controller;

use App\Message\Command\SaveOrder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Message\PurchaseConfirmationNotification;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StockTransactionController extends AbstractController
{
    #[Route('/buy', name: 'buy-stock')]
    public function index(MessageBusInterface $bus): Response
    {
        $order = new class {
            public function getId(){
                return 2;
            }

            public function getBuyer(): object 
            {
                return new class 
                {
                    public function getEmail(): string {
                        return 'erwin.macaraig@gmail.com';
                    }
                };
            }
        
        
        };
        // 1. Dispatch confirmation message 
        // $bus->dispatch(new PurchaseConfirmationNotification($order->getId()));
        $bus->dispatch(new SaveOrder());

        // 2. Display confirmation to the user 

        return $this->render('stocks/example.html.twig');
    }
}
