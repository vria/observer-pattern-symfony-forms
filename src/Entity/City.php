<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City.
 *
 * @ORM\Entity
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class City
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    public $name;

    /**
     * @var Region
     *
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="cities")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", nullable=false)
     */
    public $region;

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
