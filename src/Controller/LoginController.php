<?php

namespace App\Controller;

use App\Entity\Compte;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
class LoginController extends AbstractController
{
    #[Route('/connection', name: 'app_prof_connect')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$username = $authenticationUtils->getLastUsername();
		
		return $this->render('profile/inscri_co.html.twig', [
			'username' => $username,
			'error' => $error,
			'etat' => 'connection',
		]);
    }
}
