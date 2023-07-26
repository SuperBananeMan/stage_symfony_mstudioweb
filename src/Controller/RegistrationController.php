<?php

namespace App\Controller;

use App\Form\Type\UserType;
use App\Entity\User;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Model\Registration;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_prof_sign')]
	public function new(SessionInterface $session, Request $request, EntityManagerInterface $entityManager, /*UserPasswordHasherInterface $passwordHasher*/): Response
    {
        // creates a user object and initializes some data for this example
        $user = new User();
		
		

        $form = $this->createForm(UserType::class, $user);
		
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$user` variable has also been updated
            $user = $form->getData();
			
			// hash the password (based on the security.yaml config for the $user class)
			$hashedPassword = $passwordHasher->hashPassword(
				$user,
				$user->getPassword(),
			);
			$user->setPassword($hashedPassword);

            // ... perform some action, such as saving the user to the database
			$entityManager->persist($user);
			$entityManager->flush();

            return $this->redirectToRoute('app_prof_show', [
				'image' => "",
				'user' => $user,
				'etat' => 'inscrit',
				'connectee' => true
			]);
		}
		return $this->render('profile/inscri_co.html.twig', [
			'image' => "",
			'form' => $form,
			'etat' => 'inscription',
			'connectee' => false
		]);
    }
	
	#[Route('/profile', name: 'app_prof_show')]
	public function show_prof(SessionInterface $session, Request $request, EntityManagerInterface $entityManager)
	{
		$username = $session->get('username');
		if($username != ''){
			$connectee = true;
		}
		else{
			$connectee = false;
		}
		$email = $session->get('email');
		$password = $session->get('password');
		$pfp = $session->get('pfp');
		$image = $session->get('pfp');
		return $this->render('profile/profile.html.twig', [
			'image' => $image,
			'username' => $username,
			'email' => $email,
			'password' => $password,
			'pfp' => $pfp,
			'etat' => 'connectee',
			'connectee' => $connectee
		]);
	}
	
	#[Route('/pfpchange', name: 'app_pfp_change')]
	public function show_prof_prof(SessionInterface $session, Request $request, EntityManagerInterface $entityManager)
	{
		$lid = $session->get('id')[0];
		$user = $entityManager->getRepository(User::class)->find($lid);

		$formpfp = $this->createFormBuilder($user)
			->add('pfp', VichFileType::class, ['label' => ' ','attr' => ['onchange' => 'loadFile(event)']])
			->add('save', SubmitType::class, ['label' => 'Save and Add'])
			->getForm();
		
		$formpfp->handleRequest($request);
		
		if ($formpfp->isSubmitted() && $formpfp->isValid()) {
			$user->setPfpName($user->getPfp()->getClientOriginalName());

			$entityManager->flush();
			
			$session->set('pfp',$user->getPfpName());
			
			$username = $session->get('username');
			$email = $session->get('email');
			$password = $session->get('password');
			$pfp = $session->get('pfp');
			$image = $session->get('pfp');
			return $this->redirectToRoute('app_prof_show', [
				'image' => $image,
				'formpfp' => $formpfp,
				'username' => $username,
				'email' => $email,
				'password' => $password,
				'pfp' => $pfp,
				'etat' => 'inscrit',
				'connectee' => true
			]);
		}
		
		$session->set('pfp',$user->getPfpName());
		
		$username = $session->get('username');
		$email = $session->get('email');
		$password = $session->get('password');
		$pfp = $session->get('pfp');
		$image = $session->get('pfp');
		return $this->render('profile/pfpchange.html.twig', [
			'image' => $image,
			'formpfp' => $formpfp,
			'username' => $username,
			'email' => $email,
			'password' => $password,
			'pfp' => $pfp,
			'etat' => 'connectee',
			'connectee' => true
		]);
	}
	
	#[Route('/other', name: 'app_prof_other')]
	public function show_prof_other(){
		return null;
	}
}