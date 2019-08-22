<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResTablesRepository")
 * @ORM\Table(name="res_tables", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="restaurant_table_number", columns={"restaurant_id", "number"})
 * })
 */
class ResTables
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     */
    private $capacity;

    /**
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     */
    private $number;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('active', 'inactive')")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Restaurant", inversedBy="tables")
     */
    private $restaurant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(string $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        if (!in_array($status, array(self::STATUS_ACTIVE, self::STATUS_INACTIVE))) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }
}
