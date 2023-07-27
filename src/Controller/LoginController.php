<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class LoginController extends AbstractController
{
    #[Route('/connection', name: 'app_prof_connect')]
    public function index(EntityManagerInterface $entityManager, SessionInterface $session, Request $request, AuthenticationUtils $authenticationUtils, UserRepository $repository): Response
    {
		// get the login error if there is one
		$error = '';

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();
		
		return $this->render('profile/inscri_co.html.twig', [
			'controller_name' => 'LoginController',
			'etat' => 'connection',
			'last_username' => $lastUsername,
			'error'         => $error,
		]);
    }
	
	#[Route('/deconnection', name: 'app_prof_deconnect')]
	public function deco(SessionInterface $session){
		$session->clear();
		
		return $this->redirectToRoute('app_prof_show', [
			'connectee' => false
		]);
	}
}
