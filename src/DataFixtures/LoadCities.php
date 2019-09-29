<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @see City
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class LoadCities extends Fixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // Cities of IDF in France
        $franceIDF = $this->getReference('region_france_idf');

        $paris = new City();
        $paris->name = 'Paris';
        $paris->region = $franceIDF;
        $manager->persist($paris);

        $chaville = new City();
        $chaville->name = 'Chaville';
        $chaville->region = $franceIDF;
        $manager->persist($chaville);

        $sevres = new City();
        $sevres->name = 'Sèvres';
        $sevres->region = $franceIDF;
        $manager->persist($sevres);

        // Cities of HDF in France
        $franceHDF = $this->getReference('region_france_hdf');

        $lille = new City();
        $lille->name = 'Lille';
        $lille->region = $franceHDF;
        $manager->persist($lille);

        $amiens = new City();
        $amiens->name = 'Amiens';
        $amiens->region = $franceHDF;
        $manager->persist($amiens);

        // Cities of Normandie in France
        $franceNormandie = $this->getReference('region_france_normandie');

        $caen = new City();
        $caen->name = 'Caen';
        $caen->region = $franceNormandie;
        $manager->persist($caen);

        // Cities of Baden-Württemberg in Germany
        $germanyBadenWurttemberg = $this->getReference('region_germany_baden_wurttemberg');

        $stuttgart = new City();
        $stuttgart->name = 'Stuttgart';
        $stuttgart->region = $germanyBadenWurttemberg;
        $manager->persist($stuttgart);

        // Cities of Bavaria in Germany
        $germanyBavaria = $this->getReference('region_germany_bavaria');

        $munich = new City();
        $munich->name = 'Munich';
        $munich->region = $germanyBavaria;
        $manager->persist($munich);

        $nuremberg = new City();
        $nuremberg->name = 'Nürnberg';
        $nuremberg->region = $germanyBavaria;
        $manager->persist($nuremberg);

        $augsburg = new City();
        $augsburg->name = 'Augsburg';
        $augsburg->region = $germanyBavaria;
        $manager->persist($augsburg);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
