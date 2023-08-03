<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

use App\Entity\User;
use App\Entity\Videos;
use App\Form\UserType;
use App\Repository\CommentsRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\String\Slugger\SluggerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use function Symfony\Component\String\u;
use App\Repository\VideosRepository;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_prof_sign')]
	public function new(MailerInterface $mailer, VerifyEmailHelperInterface $verifyEmailHelper, SessionInterface $session, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, AuthenticationUtils $authenticationUtils): Response
    {
		// get the login error if there is one
		$error = '';

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();
		
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

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $email = (new TemplatedEmail())
                ->from('damien.lolz.destructor@gmail.com')
                ->to($user->getEmail())
                ->subject('Welcome to VideoTube !')
                ->htmlTemplate('email/welcome.html.twig')
                ->context([
                    'user' => $user,
                    'signatureLink' => $signatureComponents->getSignedUrl(),
                ]);
            $mailer->send($email);

            return $this->redirectToRoute('app_prof_show', [
				'image' => "",
				'user' => $user,
				'pfpName' => '',
				'etat' => 'inscrit',
				'connectee' => true
			]);
		}
		
		return $this->render('profile/inscri_co.html.twig', [
			'formSearch' => '',
			'pfpName' => '',
			'form' => $form,
			'etat' => 'inscription',
			'connectee' => false
		]);
    }

    #[Route("/verify", name:"app_verify_email")]
    public function verifyUserEmail(EntityManagerInterface $entityManager, Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('danger', $e->getReason());
            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Your account is now verified !');
        return $this->redirectToRoute('app_prof_show');
    }
	
	#[Route('/profile', name: 'app_prof_show')]
	public function show_prof(SessionInterface $session, CommentsRepository $commentRepository, Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, VideosRepository $videoRepository, string $slug = null)
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();
		
		if($username != ''){
			$connectee = true;
		}
		else{
			$connectee = false;
		}

        $queryBuilder = $videoRepository->createOrderedByQueryBuilder(null,$username);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('', 1),
            9
        );
		
		$queryBuildercomment = $commentRepository->commentTakerByUser($username->getId());
        $adaptercomment = new QueryAdapter($queryBuildercomment);
        $pagerfantacomment = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adaptercomment,
            $request->query->get('page', 1),
			10
        );
		
		$numbers = array();
		
		foreach($pagerfantacomment as $comms){
			$numbers[] = $username->getPfpName();
		}

        $formSearch = $this->createForm(SearchType::class);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $formSearch->getData();
			return $this->redirectToRoute('app_search', [
				'dataSearch' => $data,
				'formSearch' => $formSearch,
				'pfpName' => $username->getPfpName(),
			]);
        }
		
		return $this->render('profile/profile.html.twig', [
			'formSearch' => $formSearch,
			'ogUser' => true,
			'numbers' => $numbers,
			'userid' => $username->getId(),
			'username' => $username->getUsername(),
			'pfpName' => $username->getPfpName(),
			'etat' => 'connectee',
			'connectee' => $connectee,
			'pager' => $pagerfanta,
			'pagercomment' => $pagerfantacomment,
		]);
	}
	
	#[Route('/profile/{id}', name: 'app_prof_show_other')]
	public function show_prof_other(SessionInterface $session, CommentsRepository $commentRepository, Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, VideosRepository $videoRepository, string $id = null, string $slug = null)
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();
		
		if($id == $username->getId()){
			return $this->redirectToRoute('app_prof_show');
		}
		
		$utilisateur = $entityManager->getRepository(User::class)->find($id);
		
		$genre = $slug ? u(str_replace('-', ' ', $slug))->title(true) : null;
        $queryBuilder = $videoRepository->createOrderedByQueryBuilder($slug, $utilisateur);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('', 1),
            9
        );
		
		$queryBuildercomment = $commentRepository->commentTakerByUser($utilisateur->getId());
        $adaptercomment = new QueryAdapter($queryBuildercomment);
        $pagerfantacomment = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adaptercomment,
            $request->query->get('page', 1),
			10
        );
		
		$numbers = array();
		
		foreach($pagerfantacomment as $comms){
			$numbers[] = $utilisateur->getPfpName();
		}

        $formSearch = $this->createForm(SearchType::class);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $formSearch->getData();
			return $this->redirectToRoute('app_search', [
				'dataSearch' => $data,
				'formSearch' => $formSearch,
				'pfpName' => $username->getPfpName(),
			]);
        }
		
		return $this->render('profile/profile.html.twig', [
			'formSearch' => $formSearch,
			'ogUser' => false,
			'numbers' => $numbers,
			'userid' => $utilisateur->getId(),
			'username' => $utilisateur->getUsername(),
			'pfpName' => $username->getPfpName(),
			'pfpNameOther' => $utilisateur->getPfpName(),
			'etat' => 'connectee',
			'pager' => $pagerfanta,
			'pagercomment' => $pagerfantacomment,
		]);
	}
	
	#[Route('/pfpchange', name: 'app_pfp_change')]
	public function show_prof_prof(SessionInterface $session, Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, Filesystem $filesystem)
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();

        $formSearch = $this->createForm(SearchType::class);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $formSearch->getData();
			return $this->redirectToRoute('app_search', [
				'formSearch' => $formSearch,
				'pfpName' => $username->getPfpName(),
			]);
        }
		
		$saveOlfPfp = $username->getPfpName();
		
		$user = $entityManager->getRepository(User::class)->find($username);

		$formpfp = $this->createFormBuilder($user)
			->add('pfp', VichFileType::class, ['label' => ' ','attr' => ['onchange' => 'loadFile(event)']])
			->add('save', SubmitType::class, ['label' => 'Save and Add'])
			->add('reset', ResetType::class, ['label' => 'Delete File','attr' => ['class' => 'btn btn-secondary']])
			->getForm();
		
		$formpfp->handleRequest($request);
		
		if ($formpfp->isSubmitted() && $formpfp->isValid()) {
			$user->setPfpName($user->getPfp()->getClientOriginalName());
			
			$entityManager->flush();
			
			$newpfpName = $user->getPfpName();
			
			$file = $user->getPfp();
			
			$user->setPfp(null);
			
			$filesystem->remove("C:/Users/damie/mixed_vinyl/public/pfp/{$saveOlfPfp}");
			
			return $this->redirectToRoute('app_prof_show', [
				'pfpName' => $newpfpName,
				'file' => $file,
				'formpfp' => $formpfp,
				'etat' => 'inscrit',
				'connectee' => true
			]);
		}
		
		return $this->render('profile/pfpchange.html.twig', [
			'formSearch' => $formSearch,
			'pfpName' => $username->getPfpName(),
			'formpfp' => $formpfp,
			'etat' => 'connectee',
			'connectee' => true
		]);
	}
}