<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Location.
 *
 * @ORM\Entity
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class Location
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * In `Location` entity the only field is needed is `city`.
     * Country and regtion are inferred from it because there is a direct relation :.
     *
     * @see City::$region
     * @see Region::$country
     *
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=false)
     */
    public $city;

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
