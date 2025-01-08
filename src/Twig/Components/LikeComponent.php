<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Psr\Log\LoggerInterface;


#[AsLiveComponent]
final class LikeComponent
{
    use DefaultActionTrait;

    public $post;

    #[LiveProp(writable: true)]
    public $likes;

    #[LiveProp(writable: true)]
    public $dislikes; 

    #[LiveAction]
    public function like(LoggerInterface $logger)
    {
        $this->likes++;        
    }

    #[LiveAction] 
    public function undoLike()
    {
        $this->likes--;
    }

    #[LiveAction] 
    public function dislike() 
    {
        $this->dislikes++;
    }

    #[LiveAction] 
    public function undoDislike() 
    {
        $this->dislikes--;
    }

    public function getRandomNumber(): int
    {
        return rand(0, 1000);
    }

}
