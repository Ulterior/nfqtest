<?php

namespace App\Entity;

use App\Entity\ResTables as RestaurantTable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RestaurantRepository")
 */
class Restaurant
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('active', 'inactive')")
     */
    private $status;

        /**
     * @ORM\OneToMany(targetEntity="App\Entity\ResTables", mappedBy="restaurant", orphanRemoval=true)
     */
    private $tables;

    public function __construct()
    {
        $this->tables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

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

    /**
     * @return Collection|RestaurantTable[]
     */
    public function getTables()
    {
        return $this->tables;
    }

    public function getNumActiveTables(): int
    {
      $activeCount = 0;
      foreach($this->tables as $table) {
        if($table->getStatus() == RestaurantTable::STATUS_ACTIVE)
          $activeCount++;
      }
      return $activeCount;
    }

}
