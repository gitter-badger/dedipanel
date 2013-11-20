<?php

namespace DP\VoipServer\MumbleServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DP\VoipServer\VoipServerBundle\Entity\VoipServer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DP\VoipServer\MumbleServerBundle\Entity\Mumble
 * 
 * @ORM\Table(name="mumble_server")
 * @ORM\Entity(repositoryClass="DP\VoipServer\MumbleServerBundle\Entity\MumbleRepository")
 */
class Mumble extends VoipServer
{
    /**
     * @var integer portIce
     *
     * @ORM\Column(name="portIce", type="integer")
	   * @Assert\Range(
     *      min = 1, minMessage = "voipserver.assert.port",
     *      max = 65536, maxMessage = "voipserver.assert.port"
     * )
     */
    private $portIce = 6502;
    /**
     * @var string iceSecret
     *
     * @ORM\Column(name="iceSecret", type="string", length=32, nullable=true)
     */
    private $iceSecret;

    /**
     * Set portIce
     *
     * @param integer $portIce
     * @return MumbleServer
     */
    public function setPortIce($portIce)
    {
        $this->portIce = $portIce;
    
        return $this;
    }

    /**
     * Get portIce
     *
     * @return integer 
     */
    public function getPortIce()
    {
        return $this->portIce;
    }

    /**
     * Set iceSecret
     *
     * @param string $iceSecret
     * @return MumbleServer
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
}

