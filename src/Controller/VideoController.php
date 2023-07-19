<?php

namespace App\Controller;

use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use function Symfony\Component\String\u;

use App\Entity\Videos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VideosRepository;

class VideoController extends AbstractController
{
	public function __construct(
        private bool $isDebug
    )
    {}
	
	#[Route('/browse/{slug}', name: 'app_browse')]
    public function browse(VideosRepository $videoRepository, Request $request, string $slug = null): Response
    {
        $genre = $slug ? u(str_replace('-', ' ', $slug))->title(true) : null;
        $queryBuilder = $videoRepository->createOrderedByQueryBuilder($slug);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            9
        );
        return $this->render('vinyl/browse.html.twig', [
            'genre' => $genre,
            'pager' => $pagerfanta,
        ]);
    }
	
    #[Route('/video/new')]
    public function new(EntityManagerInterface $entityManager): Response
    {
        $video = new Videos();
		$video->setNom('Two man, two toilets...');
        $video->setTitle('trim.DA6FBB84-7088-40F0-A7B4-A3C7A1407BE8.mov');
        $video->setDescription('You have to...');
		$video->setGenre('meme');

        $entityManager->persist($video);
        $entityManager->flush();

        return new Response(sprintf(
            'Only the strongest will win.',
            $video->getId(),
        ));
    }
	
	#[Route('/video/{slug}', name: 'app_video_show')]
    public function show(Videos $video): Response
	{
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }
}
