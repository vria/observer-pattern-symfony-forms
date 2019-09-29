<?php

namespace App\Form\Type;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Location;
use App\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Form type for create a new location or edit the existing one.
 *
 * @see Location
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class LocationType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Instead of adding the fields `country`, `region` and `city` directly
        // in this method we will delegate this to event listeners because the
        // options of these fields depend on underlying data:
        // - when the form is rendered this is the data stored in database,
        // - when the form is submitted this is the data from request
        //
        // There are few good reasons for this :
        //
        // 1. Neither `country` nor `region` are stored in location and mapped
        // to form. It is necessary to infer `country` and `region` values for
        // the given city in location and assign them to form fields. Otherwise
        // these fields will be rendered empty - any option will be selected.
        //
        // 2. We need to limit the number of options in `region` field based on
        // a chosen country as well as to limit the number of options in `city`
        // field based on a chosen region.
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit'])
        ;
    }

    /**
     * FormEvents::PRE_SET_DATA listener. This method adds country`, `region`
     * and `city` fields based on data stored in database.
     *
     * For example, when an existing location reference the city of "Paris" then
     * the region field must be set to "Île-de-France" and the country field
     * must be set to "France".
     *
     * At the same time the choice of cities will be limited to "Paris",
     * "Versailles", "Sèvres" an other cities of "Île-de-France" region. The
     * regions will be limited to "Île-de-France", "Hauts-de-France",
     * "Normandie" and other regions of the country of "France".
     *
     * When the location is being created, the lists of regions and cities will
     * be empty because the country will not be selected.
     *
     * @param FormEvent $event
     *
     * @author Vlad Riabchenko <vriabchenko@webnet.fr>
     */
    public function onPreSetData(FormEvent $event)
    {
        /** @var Location $location */
        $location = $event->getData();
        $city = $location instanceof Location ? $location->city : null;
        $region = $city ? $city->region : null;
        $country = $region ? $region->country : null;

        $this->addCountry($event->getForm(), $country);
        $this->addRegion($event->getForm(), $country, $region);
        $this->addCity($event->getForm(), $region);
    }

    /**
     * FormEvents::PRE_SUBMIT listener.
     *
     * This listener updates all lists based on the request data. In fact, when
     * a user submits the form then a country, a region and a city may change.
     * If the a country has changed, then the list of regions will be different
     * than it was when the form was populated.
     * If you leave the old list with old values and send a value that is beyond
     * this list then the validation error will pop up.
     *
     * @param FormEvent $event
     *
     * @author Vlad Riabchenko <vriabchenko@webnet.fr>
     */
    public function onPreSubmit(FormEvent $event)
    {
        // The submitted data from request
        $requestData = $event->getData();

        /** @var Country $country */
        $country = !empty($requestData['country'])
            ? $this->em->getRepository(Country::class)->find($requestData['country'])
            : null;

        /** @var Region $region */
        $region = !empty($requestData['region'])
            ? $this->em->getRepository(Region::class)->find($requestData['region'])
            : null;

        $this->addCountry($event->getForm());
        $this->addRegion($event->getForm(), $country, $region);
        $this->addCity($event->getForm(), $region);
    }

    /**
     * Add `country` field to the form.
     *
     * @param FormInterface $form
     * @param Country|null  $country
     */
    private function addCountry(FormInterface $form, Country $country = null)
    {
        $form->add('country', EntityType::class, [
            'class' => Country::class,
            'choice_label' => 'name',
            'mapped' => false,
            'required' => true,
            'placeholder' => 'Select a country',
            'data' => $country,
        ]);
    }

    /**
     * Add `region` field to the form.
     *
     * @param FormInterface $form
     * @param Country|null  $country
     * @param Region|null   $region
     */
    private function addRegion(FormInterface $form, Country $country = null, Region $region = null)
    {
        $form
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'name',
                'mapped' => false,
                'required' => true,
                'placeholder' => 'Select a region',
                'data' => $region,
                'query_builder' => function (EntityRepository $repository) use ($country) {
                    return $repository
                        ->createQueryBuilder('r')
                        ->andWhere('r.country = :country')
                        ->setParameter('country', $country)
                    ;
                },
            ])
        ;
    }

    /**
     * Add `city` field to the form.
     *
     * @param FormInterface $form
     * @param Region|null   $region
     */
    private function addCity(FormInterface $form, Region $region = null)
    {
        $form->add('city', EntityType::class, [
            'class' => City::class,
            'choice_label' => 'name',
            'placeholder' => 'Select a city',
            'query_builder' => function (EntityRepository $repository) use ($region) {
                return $repository
                    ->createQueryBuilder('c')
                    ->andWhere('c.region = :region')
                    ->setParameter('region', $region)
                ;
            },
        ]);
    }
}
