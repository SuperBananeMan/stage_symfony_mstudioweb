<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use App\Repository\VinylMixRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class VinylController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(SessionInterface $session, UserRepository $repository, AuthenticationUtils $authenticationUtils): Response
    {
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();
		
        return $this->render('vinyl/homepage.html.twig', [
			'pfpName' => $username->getPfpName(),
            'title' => 'VideoTube',
        ]);
    }
}
