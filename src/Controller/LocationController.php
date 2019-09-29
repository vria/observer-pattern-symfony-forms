<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Location;
use App\Entity\Region;
use App\Form\Type\LocationType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * The controller to manage locations:
 * - list locations @see LocationController::listAction()
 * - create location @see LocationController::createAction()
 * - edit existing location @see LocationController::editAction().
 *
 * In addition there are two actions to retreive :
 * - the list of regions of given country @see LocationController::getRegionsForCountryAction()
 * - the list of cities of given region @see LocationController::getCitiesForRegionAction()
 * These methods are called by AJAX from the page of location creation/edit.
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class LocationController
{
    /**
     * Lists all locations.
     *
     * @Route("/", methods={"GET"}, name="location_list")
     * @Template()
     *
     * @param EntityManagerInterface $em
     *
     * @return array
     */
    public function listAction(EntityManagerInterface $em)
    {
        $locations = $em->getRepository(Location::class)->findAll();

        return [
            'locations' => $locations,
        ];
    }

    /**
     * Create location.
     *
     * @Route("/create", methods={"GET", "POST"}, name="location_create")
     * @Template()
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface   $formFactory
     * @param UrlGeneratorInterface  $urlGenerator
     *
     * @return array|RedirectResponse
     */
    public function createAction(Request $request, EntityManagerInterface $em, FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator)
    {
        $location = new Location();
        $form = $formFactory->create(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();

            return new RedirectResponse($urlGenerator->generate('location_list'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Edit location.
     *
     * @Route("/edit/{id}",
     *     methods={"GET", "PUT"},
     *     name="location_edit",
     *     requirements={"id"="\d+"}
     * )
     * @Template()
     *
     * @param Location               $location
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface   $formFactory
     * @param UrlGeneratorInterface  $urlGenerator
     *
     * @return array|RedirectResponse
     */
    public function editAction(Location $location, Request $request, EntityManagerInterface $em, FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator)
    {
        $form = $formFactory->create(LocationType::class, $location, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return new RedirectResponse($urlGenerator->generate('location_list'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Returns all regtions for given country as json array:
     * {'regions': [{'id': 1, 'name' => 'First'}, {'id': 2, 'name' => 'Second'}, ...]}
     * Called from /web/js/location_form.js.
     *
     * @Route("/get_regions_for_country/{id}",
     *     methods={"GET"},
     *     name="location_get_regions_for_country",
     *     requirements={"id"="\d+"},
     *     options={"expose"=true}
     * )
     *
     * @param Country $country
     *
     * @return JsonResponse
     */
    public function getRegionsForCountryAction(Country $country)
    {
        $regions = $country->regions
            ->map(function (Region $region) {
                return ['id' => $region->getId(), 'name' => $region->name];
            })
            ->toArray()
        ;

        return new JsonResponse(['regions' => $regions]);
    }

    /**
     * Returns all cities for given region as json array:
     * {'cities': [{'id': 1, 'name' => 'First'}, {'id': 2, 'name' => 'Second'}, ...]}
     * Called from /web/js/location_form.js.
     *
     * @Route("/get_cities_for_region/{id}",
     *     methods={"GET"},
     *     name="location_get_cities_for_region",
     *     requirements={"id"="\d+"},
     *     options={"expose"=true}
     * )
     *
     * @param Region $region
     *
     * @return JsonResponse
     */
    public function getCitiesForRegionAction(Region $region)
    {
        $cities = $region->cities
            ->map(function (City $city) {
                return ['id' => $city->getId(), 'name' => $city->name];
            })
            ->toArray()
        ;

        return new JsonResponse(['cities' => $cities]);
    }
}
