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
     * @Column(type="string", name="name", length=100, nullable=false)
     */
    protected $name;

    /**
     * @Column(type="string", name="airport_code", length=5, nullable=false)
     */
    protected $airport_code;

    /**
     * @Column(type="float", name="lon", nullable=false)
     */
    protected $lon;

    /**
     * @Column(type="float", name="lat", nullable=false)
     */
    protected $lat;

    /**
     * @Column(type="string", name="country", length=100, nullable=false)
     */
    protected $country;

    /**
     * @Column(type="string", name="state", length=100, nullable=true)
     */
    protected $state;

    /**
     * @Column(type="string", name="city", length=100, nullable=false)
     */
    protected $city;

    /**
     * @Column(type="string", name="timezone", length=100, nullable=false)
     */
    protected $timezone;


    public function toArray()
    {
        return [
            "id"           => $this->getId(),
            "name"         => $this->getName(),
            "airport_code" => $this->getAirportCode(),
            "lon"          => $this->getLon(),
            "lat"          => $this->getLat(),
            "country"      => $this->getCountry(),
            "state"        => $this->getState(),
            "city"         => $this->getCity(),
            "timezone"     => $this->getTimezone()
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

    /**
     * Gets the value of airport_code.
     *
     * @return string
     */
    public function getAirportCode()
    {
        return $this->airport_code;
    }

    /**
     * Gets the value of lon.
     *
     * @return float
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Gets the value of lat.
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Gets the value of country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Gets the value of state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Gets the value of city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Gets the value of timezone.
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
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

    /**
     * Sets the value of airport_code.
     *
     * @param string $airport_code the airport_code
     *
     * @return self
     */
    public function setAirportCode($airport_code)
    {
        $this->airport_code = $airport_code;

        return $this;
    }

    /**
     * Sets the value of lon.
     *
     * @param float $lon the lon
     *
     * @return self
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Sets the value of lat.
     *
     * @param float $lat the lat
     *
     * @return self
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Sets the value of country.
     *
     * @param string $country the country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Sets the value of state.
     *
     * @param string $state the state
     *
     * @return self
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Sets the value of city.
     *
     * @param string $city the city
     *
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Sets the value of timezone.
     *
     * @param string $timezone the timezone
     *
     * @return self
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }
}
