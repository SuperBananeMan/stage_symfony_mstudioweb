<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\VinylMix;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VinylMixRepository;

class MixController extends AbstractController
{
    #[Route('/mix/new')]
    public function new(EntityManagerInterface $entityManager): Response
    {
        $mix = new VinylMix();
		$titles = ['Ze healing is not as revarding as ze hurting.','Think fast chucklenuts','I like trains','Professionals have standarts.'];
        $mix->setTitle($titles[array_rand($titles)]);
		$descriptions = ['I will hurt, maybe.','I have no idea','Hi this is Patrick',"The engineer's a bloody sentry ! OHOHOHOHOHOHOH !"];
        $mix->setDescription($descriptions[array_rand($descriptions)]);
        $genres = ['pop','rock','metal','rap'];
		$mix->setGenre($genres[array_rand($genres)]);
        $mix->setTrackCount(rand(5, 25));
        $mix->setVotes(rand(0, 100));

        $entityManager->persist($mix);
        $entityManager->flush();

        return new Response(sprintf(
            'Mix %d is %d tracks of pure 80\'s heaven',
            $mix->getId(),
            $mix->getTrackCount()
        ));
    }
	
	#[Route('/mix/{slug}', name: 'app_mix_show')]
    public function show(VinylMix $mix): Response
	{
        return $this->render('mix/show.html.twig', [
            'mix' => $mix,
        ]);
    }
	
	#[Route('/mix/{slug}/vote', name: 'app_mix_vote', methods: ['POST'])]
    public function vote(VinylMix $mix, Request $request, EntityManagerInterface $entityManager): Response
    {
        $direction = $request->request->get('direction', 'up');
        if ($direction === 'up') {
            $mix->upVote();
        } else {
            $mix->downVote();
        }
        $entityManager->flush();
		$this->addFlash('success', 'Vote counted!');
		
        return $this->redirectToRoute('app_mix_show', [
            'slug' => $mix->getSlug(),
        ]);
    }
}
