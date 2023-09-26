<?php

namespace App\Controller;

use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\UserRepository;
use App\Repository\CommentsRepository;

use App\Entity\User;
use App\Entity\Comments;
use App\Entity\Videos;
use App\Form\VideoType;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Model\Registration;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use function Symfony\Component\String\u;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VideosRepository;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class VideoController extends AbstractController
{
	public function __construct(
        private bool $isDebug
    )
    {}
	
	#[Route('/browse/{genre?}', name: 'app_browse')]
	public function browse(SessionInterface $session, VideosRepository $videoRepository, Request $request, User $username = null, string $genre = null, AuthenticationUtils $authenticationUtils): Response
    {
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //$genre = $slug ? u(str_replace('-', ' ', $slug))->title(true) : null;
        $queryBuilder = $videoRepository->createOrderedByQueryBuilder($genre, $username);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            9
        );

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

		
        return $this->render('vinyl/browse.html.twig', [
            'formSearch' => $formSearch,
			'genre' => $genre,
            'pager' => $pagerfanta,
        ]);
    }

    #[Route('/myvideos/{genre?}', name: 'app_browse_myvideos')]
    public function myVideos(Request $request, VideosRepository $videoRepository, string $genre = null): Response
    {
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

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $username = $this->getUser();

        $queryBuilder = $videoRepository->createOrderedByQueryBuilder($genre, $username);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            9
        );

        return $this->render('vinyl/browsemyvideos.html.twig', [
            'genre' => $genre,
            'pager' => $pagerfanta,
            'formSearch' => $formSearch,
        ]);
    }
	
	#[Route('/video/newform', name: 'app_video_new_form')]
	public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, UserRepository $repository, AuthenticationUtils $authenticationUtils): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();

        if($username->isIsVerified() == false){
            $this->addFlash('danger', 'Please verify your account via the link sent to your email.');
            return $this->redirectToRoute('app_prof_show');
        }

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
		
		$video = new Videos();
        $video->setUser($username);

        $form = $this->createForm(VideoType::class, $video);
		
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$listeExt = array('.mp4','.mov','.webm');
			$lien = str_replace($listeExt, ".png", $video->getTitle());
			$video->setThumbnail($lien);
            
			// $form->getData() holds the submitted values
            // but, the original `$user` variable has also been updated
            $video = $form->getData();
			
			$entityManager->persist($video);
			$entityManager->flush();
			
			
			
			return $this->redirectToRoute('app_video_new', [
				'video' => $video,
				'slug' => $video->getSlug(),
			]);
		}
		
		return $this->render('video/videoaddForm.html.twig', [
			'formSearch' => $formSearch,
			'form2' => $form,
		]);
	}
	
    #[Route('/video/new/{slug}', name: 'app_video_new')]
    public function newshow(Request $request, SessionInterface $session, Videos $video, AuthenticationUtils $authenticationUtils): Response
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
		
		return $this->render('video/videoadd.html.twig', [
			'formSearch' => $formSearch,
			'video' => $video,
		]);
    }
	
	#[Route('/video/{slug}', name: 'app_video_show')]
    public function show(SessionInterface $session, Videos $video, Security $security, CommentsRepository $commentRepository, Request $request, EntityManagerInterface $entityManager, UserRepository $repository, AuthenticationUtils $authenticationUtils): Response
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
		
		$comment = new Comments();
        $comment->setUser($username);
        $comment->setVideo($video);
		
		$formcom = $this->createForm(CommentType::class, $comment);
		
		$formcom->handleRequest($request);
		if ($formcom->isSubmitted() && $formcom->isValid()) {
			// $formcom->getData() holds the submitted values
            // but, the original `$comment` variable has also been updated
            $comment = $formcom->getData();

            // ... performcom some action, such as saving the comment to the database
			$entityManager->persist($comment);
			$entityManager->flush();
			
			unset($comment);
			unset($formcom);
			$comment = new Comments();
			$formcom = $this->createForm(CommentType::class, $comment);
			
			
			
			return $this->redirectToRoute('app_video_show', [
				'slug' => $video->getSlug(),
				'formcom' => $formcom,
				'video' => $video,
			]);
		}
		
        $queryBuilder = $commentRepository->commentTaker($video->getId());
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            10
        );

        return $this->render('video/show.html.twig', [
			'formSearch' => $formSearch,
			'formcom' => $formcom,
			'video' => $video,
			'pager' => $pagerfanta,
        ]);
    }
}
