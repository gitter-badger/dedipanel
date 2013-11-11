<?php

namespace DP\VoipServer\MumbleServerBundle\Entity\Mumble;

use Doctrine\ORM\Mapping as ORM;

/**
 * Machine
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Machine
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="icePort", type="integer")
     */
    private $icePort;

    /**
     * @var string
     *
     * @ORM\Column(name="iceSecret", type="string", length=255)
     */
    private $iceSecret;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="query", type="object")
     */
    private $query;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set icePort
     *
     * @param integer $icePort
     * @return Machine
     */
    public function setIcePort($icePort)
    {
        $this->icePort = $icePort;
    
        return $this;
    }

    /**
     * Get icePort
     *
     * @return integer 
     */
    public function getIcePort()
    {
        return $this->icePort;
    }

    /**
     * Set iceSecret
     *
     * @param string $iceSecret
     * @return Machine
     */
    public function setIceSecret($iceSecret)
    {
        $this->iceSecret = $iceSecret;
    
        return $this;
    }

    /**
     * Get iceSecret
     *
     * @return string 
     */
    public function getIceSecret()
    {
        return $this->iceSecret;
    }

    /**
     * Set query
     *
     * @param \stdClass $query
     * @return Machine
     */
    public function setQuery($query)
    {
        $this->query = $query;
    
        return $this;
    }

    /**
     * Get query
     *
     * @return \stdClass 
     */
    public function getQuery()
    {
        return $this->query;
    }
}
