<?php

namespace App\Controller;

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
	
	#[Route('/browse/{slug?}', name: 'app_browse')]
	public function browse(SessionInterface $session, VideosRepository $videoRepository, Request $request, string $slug = null, AuthenticationUtils $authenticationUtils): Response
    {
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();
		
        $genre = $slug ? u(str_replace('-', ' ', $slug))->title(true) : null;
        $queryBuilder = $videoRepository->createOrderedByQueryBuilder($slug, $username->getId());
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            9
        );
		
		$defaultData = ['message' => 'Type your message here'];
        $formSearch = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class)
            
            ->getForm();

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
		
        return $this->render('vinyl/browse.html.twig', [
			'formSearch' => $formSearch,
			'pfpName' => $username->getPfpName(),
			'user' => $username->getId(),
			'genre' => $genre,
            'pager' => $pagerfanta,
        ]);
    }
	
	#[Route('/video/newform', name: 'app_video_new_form')]
	public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, UserRepository $repository, AuthenticationUtils $authenticationUtils): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();
		
		$defaultData = ['message' => 'Type your message here'];
        $formSearch = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class)
            
            ->getForm();

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
		
		$video = new Videos();
		$video->setNom('');
		$video->setTitle('');
		$video->setDescription('');
		$video->setGenre('');
		$video->setVideoFile();
		$video->setVideoName('');
		$video->setVideoSize(0);
		
		// $up = $session->get('username');
		// $up = $repository->findOneBy(['username' => $up]);
		$video->setUploader($username->getId());//$up->getId()

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
				'pfpName' => $username->getPfpName(),
				'video' => $video,
				'slug' => $video->getSlug(),
			]);
		}
		
		return $this->render('video/videoaddForm.html.twig', [
			'formSearch' => $formSearch,
			'pfpName' => $username->getPfpName(),
			'form2' => $form,
		]);
	}
	
    #[Route('/video/new/{slug}', name: 'app_video_new')]
    public function newshow(Request $request, SessionInterface $session, Videos $video, AuthenticationUtils $authenticationUtils): Response
    {
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();
		
		$defaultData = ['message' => 'Type your message here'];
        $formSearch = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class)
            
            ->getForm();

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
		
		return $this->render('video/videoadd.html.twig', [
			'formSearch' => $formSearch,
			'pfpName' => $username->getPfpName(),
			'video' => $video,
		]);
    }
	
	#[Route('/video/{slug}', name: 'app_video_show')]
    public function show(SessionInterface $session, Videos $video, Security $security, CommentsRepository $commentRepository, Request $request, EntityManagerInterface $entityManager, UserRepository $repository, AuthenticationUtils $authenticationUtils): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();
		
		$defaultData = ['message' => 'Type your message here'];
        $formSearch = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class)
            
            ->getForm();

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
		
		$comment = new Comments();
		$comment->setUserNameComment($username->getUsername());
		$comment->setContentComment('');
		
		$comment->setUploaderComment($username->getId());
		
		$comment->setVideoComment($video->getId());
		
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
				'pfpName' => $username->getPfpName(),
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
		
		$numbers = array();
		
		foreach($pagerfanta as $comms){
			$numbers[] = $repository->find($comms->getUploaderComment())->getPfpName();
		}
		
		$creator = $repository->find($video->getUploader());

        return $this->render('video/show.html.twig', [
			'uploaderId' => $creator->getId(),
			'uploaderPfpName' => $creator->getPfpName(),
			'uploaderUsername' => $creator->getUsername(),
			'formSearch' => $formSearch,
			'pfpName' => $username->getPfpName(),
			'numbers' => $numbers,
			'formcom' => $formcom,
			'video' => $video,
			'pager' => $pagerfanta,
        ]);
    }
}
