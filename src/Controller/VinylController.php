<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comments;
use App\Entity\Videos;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use App\Repository\VideosRepository;
use App\Repository\CommentsRepository;
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
        ]);
    }
	
	#[Route('/search', name: 'app_search')]
	public function search(Request $request,CommentsRepository $commentsRepository,VideosRepository $videosRepository,UserRepository $repository)
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();
		
		$queryBuildercomment = $commentsRepository->commentTakerAll();
        $adaptercomment = new QueryAdapter($queryBuildercomment);
        $pagerfantacomment = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adaptercomment,
            $request->query->get('page', 1),
			10
        );
		
		$queryBuilderuser = $repository->userTakerAll();
        $adapteruser = new QueryAdapter($queryBuilderuser);
        $pagerfantauser = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapteruser,
            $request->query->get('page', 1),
			10
        );
		
		$queryBuildervideo = $videosRepository->videoTakerAll();
        $adaptervideo = new QueryAdapter($queryBuildervideo);
        $pagerfantavideo = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adaptervideo,
            $request->query->get('page', 1),
			10
        );
		
		dd($queryBuildercomment,$queryBuilderuser,$queryBuildervideo);
		
		return $this->render('search.html.twig', [
			'pagercomment' => $pagerfantacomment,
			'pageruser' => $pagerfantauser,
			'pagervideo' => $pagerfantavideo,
			'pfpName' => $username->getPfpName(),
		]);
	}
}
