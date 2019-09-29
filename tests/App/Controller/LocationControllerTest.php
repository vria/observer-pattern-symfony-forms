<?php

namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @see \App\Controller\LocationController
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class LocationControllerTest extends WebTestCase
{
    /**
     * @see \App\DataFixtures\LoadCountries
     */
    const COUNTRY_FRANCE = 1;

    /**
     * @see \App\DataFixtures\LoadRegions
     */
    const REGION_HDF = 2; // Hauts-de-France
    const REGION_BAVARIA = 5;

    /**
     * @see \App\DataFixtures\LoadCities
     */
    const CITY_PARIS = 1;
    const CITY_LILLE = 4;
    const CITY_MUNICH = 8;

    /**
     * Check the list page.
     *
     * @see \App\Controller\LocationController::listAction()
     */
    public function testListAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('List locations', $crawler->filter('h1')->text());
        $this->assertEquals(0, $crawler->filter('table tbody tr')->count());
    }

    /**
     * Check the location adding page.
     *
     * @see \App\Controller\LocationController::createAction()
     */
    public function testCreateAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('New location', $crawler->filter('h1')->text());

        // There must be two countries and placeholder
        $countryOptions = $crawler->filter('#location_country option');
        $this->assertEquals('Select a country', $countryOptions->eq(0)->text());
        $this->assertEquals('France', $countryOptions->eq(1)->text());
        $this->assertEquals('Germany', $countryOptions->eq(2)->text());

        // As for regions and cities there must be only placeholders
        $this->assertEquals(1, $crawler->filter('#location_region option')->count());
        $this->assertEquals(1, $crawler->filter('#location_city option')->count());
    }

    /**
     * Test adding a new city.
     *
     * @see \App\Controller\LocationController::createAction()
     */
    public function testCreatePostAction()
    {
        $client = static::createClient();

        $client->request('POST', '/create', [
            'location' => [
                'country' => self::COUNTRY_FRANCE,
                'region' => self::REGION_HDF,
                'city' => self::CITY_LILLE,
            ],
        ]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /**
     * Check that the city added in.
     *
     * @see LocationControllerTest::testCreatePostAction() is shown in the list.
     *
     * @depends testCreatePostAction
     *
     * @see \App\Controller\LocationController::listAction()
     */
    public function testListAfterOneCityAddedAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $locations = $crawler->filter('table tbody tr');

        $this->assertEquals(1, $locations->count());

        $lille = $locations->eq(0);

        $this->assertEquals('France', $lille->filter('td')->eq(1)->text());
        $this->assertEquals('Hauts-de-France', $lille->filter('td')->eq(2)->text());
        $this->assertEquals('Lille', $lille->filter('td')->eq(3)->text());
    }

    /**
     * Test of adding a new location resulting to form error at the `region` field.
     *
     * @see \App\Controller\LocationController::createAction()
     */
    public function testCreateErrorRegionAction()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/create', [
            'location' => [
                'country' => self::COUNTRY_FRANCE,
                'region'  => self::REGION_BAVARIA, // Region that does not belong to the country
                'city'    => self::CITY_MUNICH,
            ],
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $errorMessageNode = $crawler->filter('.has-error #location_region + .help-block ul li');
        $this->assertEquals(1, $errorMessageNode->count());
        $this->assertRegexp('/This value is not valid/', $errorMessageNode->text());
    }

    /**
     * Test of adding a new location resulting to form error at the `city` field.
     *
     * @see \App\Controller\LocationController::createAction()
     */
    public function testCreateErrorCityAction()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/create', [
            'location' => [
                'country' => self::COUNTRY_FRANCE,
                'region'  => self::REGION_HDF,
                'city'    => self::CITY_PARIS, // City of "Paris" that does not belong to the "Hauts-de-France" region
            ],
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $errorMessageNode = $crawler->filter('.has-error #location_city + .help-block ul li');
        $this->assertEquals(1, $errorMessageNode->count());
        $this->assertRegexp('/This value is not valid/', $errorMessageNode->text());
    }
}
