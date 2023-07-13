<?php

namespace App\DataFixtures;

use App\Entity\VinylMix;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $mix = new VinylMix();
		$titles = ['Do you Remember... Phil Collins?!','I like trains','Professionals have standarts.'];
        $mix->setTitle($titles[array_rand($titles)]);
		$descriptions = ['A pure mix of drummers turned singers!','I have no idea','Hi this is Patrick',"The engineer's a bloody sentry ! OHOHOHOHOHOHOH !"];
        $mix->setDescription($descriptions[array_rand($descriptions)]);
        $genres = ['pop','rock','metal','rap'];
		$mix->setGenre($genres[array_rand($genres)]);
        $mix->setTrackCount(rand(5, 20));
        $mix->setVotes(rand(0, 100));
		$manager->persist($mix);

        $manager->flush();
    }
}
