<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @see Country
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class LoadCountries extends Fixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $france = new Country();
        $france->name = 'France';
        $this->addReference('country_france', $france);
        $manager->persist($france);

        $germany = new Country();
        $germany->name = 'Germany';
        $this->addReference('country_germany', $germany);
        $manager->persist($germany);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
