<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Post;
use App\Entity\User;

class PostVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $post = $subject; 

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canView($post, $user);
                break;

            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canEdit($post, $user);
                break;

            case self::DELETE:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDelete($post, $user);
                break;
        }

        return false;
    }

    private function canView(Post $post, User $user): bool
    {
        if ($this->canEdit($post, $user)) {
            return true;
        }
        return false;
    }

    private function canDelete(Post $post, User $user): bool
    {
        if ($this->canEdit($post, $user)) {
            return true;
        }
        return false;
    }

    private function canEdit(Post $post, User $user)
    {
        return $user === $post->getUser();
    }
}
