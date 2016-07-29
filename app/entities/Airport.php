<?php

namespace ZoneFlight\Entities;

use Doctrine\ORM\EntityManager;
use ZoneFlight\Utils\Doctrine\AutoIncrementId;

/**
 * @Entity(repositoryClass="ZoneFlight\Repositories\AirportRepository")
 * @Table(name="Airport")
 */
class Airport implements \JsonSerializable
{
    use AutoIncrementID;

    /**
     * @Column(type="string", name="name", length=100)
     */
    protected $name;

    public function toArray()
    {
        return [
            "name" => $this->getName()
        ];
    }

    public function toIndex()
    {
        return $this->toArray();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }


    // GETTERS

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    // SETTERS

    /**
     * Sets the value of name.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
