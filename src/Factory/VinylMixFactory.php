<?php

namespace App\Factory;

use App\Entity\VinylMix;
use App\Repository\VinylMixRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<VinylMix>
 *
 * @method        VinylMix|Proxy create(array|callable $attributes = [])
 * @method static VinylMix|Proxy createOne(array $attributes = [])
 * @method static VinylMix|Proxy find(object|array|mixed $criteria)
 * @method static VinylMix|Proxy findOrCreate(array $attributes)
 * @method static VinylMix|Proxy first(string $sortedField = 'id')
 * @method static VinylMix|Proxy last(string $sortedField = 'id')
 * @method static VinylMix|Proxy random(array $attributes = [])
 * @method static VinylMix|Proxy randomOrCreate(array $attributes = [])
 * @method static VinylMixRepository|RepositoryProxy repository()
 * @method static VinylMix[]|Proxy[] all()
 * @method static VinylMix[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static VinylMix[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static VinylMix[]|Proxy[] findBy(array $attributes)
 * @method static VinylMix[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static VinylMix[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class VinylMixFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->randomElement(['Ze healing is not as revarding as ze hurting.','Think fast chucklenuts','I like trains.','Professionals have standarts.']),
			'description' => self::faker()->randomElement(['I will hurt, maybe.','I have no idea.','Hi this is Patrick.',"The engineer's a bloody sentry ! OHOHOHOHOHOHOH !"]),
            'trackCount' => self::faker()->numberBetween(5, 25),
            'genre' => self::faker()->randomElement(['pop', 'rock','metal','rap']),
            'votes' => self::faker()->numberBetween(0, 100),
            'slug' => self::faker()->randomElement(['A', 'B','C','D']),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(VinylMix $vinylMix): void {})
        ;
    }

    protected static function getClass(): string
    {
        return VinylMix::class;
    }
}
