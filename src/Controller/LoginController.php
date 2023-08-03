<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

        $formSearch = $this->createForm(SearchType::class);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $formSearch->getData();

			return $this->redirectToRoute('app_check_isVerified', [
				'dataSearch' => $data,
				'formSearch' => $formSearch,
				'pfpName' => $username->getPfpName(),
			]);
        }
		
		return $this->render('profile/inscri_co.html.twig', [
			'formSearch' => $formSearch,
			'pfpName' => '',
			'controller_name' => 'LoginController',
			'etat' => 'connection',
			'last_username' => $lastUsername,
			'error' => $error,
		]);
    }
	
	#[Route('/deconnection', name: 'app_prof_deconnect')]
	public function deco(Security $security){
        // you can also disable the csrf logout
        $response = $security->logout(false);
		
		return $this->redirectToRoute('app_prof_show', [
			'connectee' => false
		]);
	}
}
