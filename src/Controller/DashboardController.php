<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\UserFormType;
use App\Form\ImageFormType;
use App\Services\ImageUploader;
use App\Form\DeleteAccountFormType;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/dashboard/profile', name: 'app_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManagerInterface, ImageUploader $imageUploader): Response
    {
        // change Image
        $image = new Image();
        $imageForm = $this->createForm(ImageFormType::class, $image);
        $imageForm->handleRequest($request);
        $user = $this->getUser(); // this is the way how we get the currently logged in user
        if ($imageForm->isSubmitted() && $imageForm->isValid())
        {
            // $image = $imageForm->getData();
            $imageFile = $imageForm->get('imageFile')->getData();
            if ($imageFile)
            {
                if ($user->getImage()?->getPath())
                {
                    unlink($this->getParameter('images_directory'). '/' . $user->getImage()->getPath());
                }
                $newFilename = $imageUploader->upload($imageFile);
                $image->setPath($newFilename);
                if ($user->getImage())
                {
                    $oldImage = $entityManagerInterface->getRepository(Image::class)->find($user->getImage()->getId());
                    $entityManagerInterface->remove($oldImage);
                }
                $user->setImage($image);
                $entityManagerInterface->persist($image);
                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();
                $this->addFlash('status-image', 'image-updated');
            }
            return $this->redirectToRoute('app_profile');
        }

        // change email and name
        
        $userForm = $this->createForm(UserFormType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()){
            // $user = $userForm->getData();
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            $this->addFlash('status-profile-information', 'user-updated');
            return $this->redirectToRoute('app_profile');
        }

        // change password
        $passwordForm = $this->createForm(ChangePasswordFormType::class, $user);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()){
            // $user = $passwordForm->getData();
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            $this->addFlash('status-password', 'password-changed');
            return $this->redirectToRoute('app_profile');
        }

        // delete account
        $deleteAccountForm = $this->createForm(DeleteAccountFormType::class, $user);
        $deleteAccountForm->handleRequest($request);
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) 
        {
            // $user = $deleteAccountForm->getData();
            $request->getSession()->invalidate();
            return $this->redirectToRoute('blog_logout');
        }
        return $this->render('dashboard/edit.html.twig', [
            'imageForm' => $imageForm->createView(),
            'userForm' => $userForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'deleteAccountForm' => $deleteAccountForm->createView()
        ]);
    }
}

