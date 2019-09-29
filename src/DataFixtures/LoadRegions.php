<?php

namespace App\DataFixtures;

use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @see Region
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class LoadRegions extends Fixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // Regions of France
        $france = $this->getReference('country_france');

        $franceIDF = new Region();
        $franceIDF->name = 'Île-de-France';
        $franceIDF->country = $france;
        $this->addReference('region_france_idf', $franceIDF);
        $manager->persist($franceIDF);

        $franceHDF = new Region();
        $franceHDF->name = 'Hauts-de-France';
        $franceHDF->country = $france;
        $this->addReference('region_france_hdf', $franceHDF);
        $manager->persist($franceHDF);

        $franceNormandie = new Region();
        $franceNormandie->name = 'Normandie';
        $franceNormandie->country = $france;
        $this->addReference('region_france_normandie', $franceNormandie);
        $manager->persist($franceNormandie);

        // Regions of Germany
        $germany = $this->getReference('country_germany');

        $germanyBaden = new Region();
        $germanyBaden->name = 'Baden-Württemberg';
        $germanyBaden->country = $germany;
        $this->addReference('region_germany_baden_wurttemberg', $germanyBaden);
        $manager->persist($germanyBaden);

        $germanyBavaria = new Region();
        $germanyBavaria->name = 'Bavaria';
        $germanyBavaria->country = $germany;
        $this->addReference('region_germany_bavaria', $germanyBavaria);
        $manager->persist($germanyBavaria);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
