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
use App\Form\SearchType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class VinylController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(Request $request,SessionInterface $session, UserRepository $repository): Response
    {
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();

        $formSearch = $this->createForm(SearchType::class);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $formSearch->getData();
			return $this->redirectToRoute('app_search', [
				'dataSearch' => $data,
				'formSearch' => $formSearch,
			]);
        }
		
        return $this->render('vinyl/homepage.html.twig', [
			'formSearch' => $formSearch,
        ]);
    }
	
	#[Route('/search', name: 'app_search')]
	public function search(Request $request,CommentsRepository $commentsRepository,VideosRepository $videosRepository,UserRepository $repository)
	{
		$dataSearch = $request->get("dataSearch");
		if($dataSearch != null){
			$dataSearchvar = $dataSearch["search"];
		}
		else{
			$dataSearchvar = null;
		}
		
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();
		
		$queryBuildercomment = $commentsRepository->commentTakerAll($dataSearchvar);
        $adaptercomment = new QueryAdapter($queryBuildercomment);
        $pagerfantacomment = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adaptercomment,
            $request->query->get('page', 1),
			99
        );
		
		$nbComments = $pagerfantacomment->getNbResults();
		
		$queryBuilderuser = $repository->userTakerAll($dataSearchvar);
        $adapteruser = new QueryAdapter($queryBuilderuser);
        $pagerfantauser = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapteruser,
            $request->query->get('page', 1),
			99
        );
		
		$nbUsers = $pagerfantauser->getNbResults();
		
		$queryBuildervideo = $videosRepository->videoTakerAll($dataSearchvar);
        $adaptervideo = new QueryAdapter($queryBuildervideo);
        $pagerfantavideo = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adaptervideo,
            $request->query->get('page', 1),
			99
        );
		
		$nbVideos = $pagerfantavideo->getNbResults();

        $formSearch = $this->createForm(SearchType::class);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $formSearch->getData();
			return $this->redirectToRoute('app_search', [
				'dataSearch' => $data,
				'formSearch' => $formSearch,
			]);
        }
		
		return $this->render('search.html.twig', [
            "dataSearch" => $dataSearch,
			'nbComments' => $nbComments,
			'nbUsers' => $nbUsers,
			'nbVideos' => $nbVideos,
			'formSearch' => $formSearch,
			'pagercomment' => $pagerfantacomment,
			'pageruser' => $pagerfantauser,
			'pagervideo' => $pagerfantavideo,
		]);
	}
}
