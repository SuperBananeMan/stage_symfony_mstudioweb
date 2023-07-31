<?php

namespace App\Controller;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

use App\Entity\User;
use App\Entity\Videos;
use App\Form\UserType;
use App\Repository\CommentsRepository;
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

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
	public function new(Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils): Response
    {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$username = $this->getUser();
		
		return $this->render('admin/admin.html.twig', [
			'pfpName' => $username->getPfpName(),
		]);
    }
}