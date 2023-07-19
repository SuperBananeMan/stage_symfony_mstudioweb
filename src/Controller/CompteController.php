<?php

namespace App\Controller;

use App\Form\Type\CompteType;
use App\Entity\Compte;
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

class CompteController extends AbstractController
{
    #[Route('/update', name: 'app_prof_sign')]
	public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // creates a compte object and initializes some data for this example
        $compte = new Compte();
		$compte->setUserName('');
        $compte->setEmail('');
        $compte->setPassword('');

        $form = $this->createForm(CompteType::class, $compte);
		
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$compte` variable has also been updated
            $compte = $form->getData();

            // ... perform some action, such as saving the compte to the database
			$entityManager->persist($compte);
			$entityManager->flush();
			
			$un = $compte->getUserName();
			$email = $compte->getEmail();
			$passwrd = $compte->getPassword();
			
            return $this->redirectToRoute('app_prof_show', [
				'username' => $un,
				'email' => $email,
				'password' => $passwrd,
				'compte' => $compte,
				'etat' => 'inscrit',
				'connectee' => true
			]);
		}
		
		return $this->render('profile/inscri_co.html.twig', [
			'form' => $form,
			'etat' => 'inscription',
			'connectee' => false
		]);
    }
	
	#[Route('/profile', name: 'app_prof_show')]
	public function show_prof()
	{
		$user = null;
		return $this->render('profile/profile.html.twig', [
			'username' => null,
			'email' => null,
			'password' => null,
			'etat' => '',
			'connectee' => ''
		]);
	}
}