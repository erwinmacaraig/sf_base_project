<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Psr\Log\LoggerInterface;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Component\Security\Core\Security;

#[AsLiveComponent]
final class LikeComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable:true)]
    public Post $post;
    public $isLiked;
    public $isDisliked;

    #[LiveProp(writable: true)]
    public $likes;

    #[LiveProp(writable: true)]
    public $dislikes; 

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[LiveAction]
    public function like(LoggerInterface $logger)
    {
        $this->post->addUsersThatLike($this->security->getUser());  
        $this->isLiked = true;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();   
    }

    #[LiveAction] 
    public function undoLike()
    {
        $this->post->removeUsersThatLike($this->security->getUser());  
        $this->isLiked = false;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();   
    }

    #[LiveAction] 
    public function dislike() 
    {
        $this->post->addUsersThatDontLike($this->security->getUser());  
        $this->isDisliked = true;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();   
    }

    #[LiveAction] 
    public function undoDislike() 
    {
        $this->post->removeUsersThatDontLike($this->security->getUser());  
        $this->isDisliked = false;
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();   
    }

    public function getRandomNumber(): int
    {
        return rand(0, 1000);
    }

}
