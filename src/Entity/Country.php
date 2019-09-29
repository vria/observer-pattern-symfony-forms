<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Country.
 *
 * @ORM\Entity
 *
 * @author Vlad Riabchenko <contact@vria.eu>
 */
class Country
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Region", mappedBy="country")
     */
    public $regions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->regions = new ArrayCollection();
    }

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
