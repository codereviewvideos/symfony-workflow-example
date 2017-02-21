<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SalesLead
 *
 * @ORM\Table(name="sales_lead")
 * @ORM\Entity()
 */
class SalesLead
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Many Users have One Address.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var array
     *
     * @ORM\Column(name="stage", type="json_array", nullable=true)
     */
    private $stage;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text")
     */
    private $notes;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customer
     *
     * @param Customer $customer
     *
     * @return SalesLead
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set stage
     *
     * @param string|array $stage
     *
     * @return SalesLead
     */
    public function setStage($stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return array
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return SalesLead
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
}

