<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageFormType;
use App\Form\UserFormType;
use App\Form\DeleteAccountFormType;
use App\Form\ChangePasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function profile(Request $request): Response
    {
        // change Image
        $image = new Image();
        $imageForm = $this->createForm(ImageFormType::class, $image);
        $imageForm->handleRequest($request);
        if ($imageForm->isSubmitted() && $imageForm->isValid())
        {
            $image = $imageForm->getData();
            return $this->redirectToRoute('app_profile');
        }

        // change email and name
        $user = $this->getUser(); // this is the way how we get the currently logged in user
        $userForm = $this->createForm(UserFormType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()){
            $user = $userForm->getData();
            return $this->redirectToRoute('app_profile');
        }

        // change password
        $passwordForm = $this->createForm(ChangePasswordFormType::class, $user);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()){
            $user = $passwordForm->getData();
            return $this->redirectToRoute('app_profile');
        }

        // delete account
        $deleteAccountForm = $this->createForm(DeleteAccountFormType::class, $user);
        $deleteAccountForm->handleRequest($request);
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) 
        {
            $user = $deleteAccountForm->getData();
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('dashboard/edit.html.twig', [
            'imageForm' => $imageForm->createView(),
            'userForm' => $userForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'deleteAccountForm' => $deleteAccountForm->createView()
        ]);
    }
}

