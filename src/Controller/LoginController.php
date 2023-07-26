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
		$username = $authenticationUtils->getLastUsername();
		
		// creates a user object and initializes some data for this example

        $form2 = $this->createform(LoginType::class, $login);
		
		$form2->handleRequest($request);
		
		if ($form2->isSubmitted() && $form2->isValid()) {
            // $form2->getData() holds the submitted values
            // but, the original `$login` variable has also been updated
            $login = $form2->getData();

            // ... perform2 some action, such as saving the login to the database
			$verif = $repository->findByUserNameField($login->getUserName());
			$verif2 = $repository->findByEmailField($login->getEmail());
			$verif3 = $repository->findByPasswordField($login->getPassword());
			
			if (!$verif or !$verif2 or !$verif3) {
				$error = 'erreur';
				return $this->render('profile/inscri_co.html.twig', [
					'form' => 'undefined',
					'form2' => $form2,
					'etat' => 'connection',
					'error' => $error,
					'connectee' => false
				]);
			}
			
			$session->set('id',$repository->findIdByUserName($login->getUserName()));
			$session->set('username',$login->getUserName());
			$session->set('email',$login->getEmail());
			$session->set('password',$login->getPassword());
			$session->set('pfp',$repository->findByPfpNameField($login->getUserName())[0]['pfpName']);
			
			$username = $session->get('username');
			$email = $session->get('email');
			$password = $session->get('password');
			$pfp = $session->get('pfp');
			$image = $session->get('pfp');
			
            return $this->redirectToRoute('app_prof_show', [
				'image' => $image,
				'username' => $username,
				'email' => $email,
				'password' => $password,
				'pfp' => $pfp,
				'login' => $login,
				'etat' => 'inscrit',
				'connectee' => true
			]);
		}
		
		$username = $session->get('username');
		$email = $session->get('email');
		$password = $session->get('password');
		$pfp = $session->get('pfp');
		$image = $session->get('pfp');
		return $this->render('profile/inscri_co.html.twig', [
			'image' => $image,
			'form' => 'undefined',
			'form2' => $form2,
			'etat' => 'connection',
			'error' => $error,
			'connectee' => false
		]);
    }
	
	#[Route('/deconnection', name: 'app_prof_deconnect')]
	public function deco(SessionInterface $session){
		$session->clear();
		$image = $session->get('pfp');
		return $this->redirectToRoute('app_prof_show', [
			'image' => $image,
			'connectee' => false
		]);
	}
}
