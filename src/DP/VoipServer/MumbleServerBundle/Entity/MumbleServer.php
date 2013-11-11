<?php

namespace DP\VoipServer\MumbleServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DP\VoipServer\VoipServerBundle\Entity\VoipServer;
use DP\Core\MachineBundle\PHPSeclibWrapper\PHPSeclibWrapper;
use DP\VoipServer\VoipServerBundle\services\iceInitClass;
use Symfony\Component\Validator\Constraints as Assert;
use DP\VoipServer\MumbleServerBundle\Ice\IceServerClass;

/**
 * DP\MumbleServer\umbleServerBundle\Entity\MumbleServer
 * 
 * @ORM\Table(name="mumble_server")
 * @ORM\Entity(repositoryClass="DP\MumbleServer\MumbleServerBundle\Entity\MumbleServerRepository")
 */
class MumbleServer extends VoipServer {
    /**
     * @var integer portIce
     *
     * @ORM\Column(name="portIce", type="integer")
     * @Assert\Min(limit=1, message="voipserver.assert.port")
     * @Assert\Max(limit=65536, message="voipserver.assert.port")
     */
    private $portIce = 6502;
    /**
     * @var string iceSecret
     *
     * @ORM\Column(name="iceSecret", type="string", length=32, nullable=true)
     */
    private $iceSecret;
	
  	/**
  	 * @var IceServerClass $query
  	 */
  	private $query;

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

	/**
	 * Sets associated IceServer instance associated to the current MumbleServer
	 *
	 * @param IceServerClass $query Ice Server instance associated to the current Mumble Server
	 * 
	 * @return MumbleServer Current MumbleServer instance, for method chaining
	 */	
	public function setQuery(IceServerClass $query)
	{
		$this->query = $query;
		
		return $this;
	}
	
	/**
	 * Gets the current associated IceServer instance
	 *
	 * @return IceServerClass Ice Server instance associated to the current Mumble Server
	 */
	public function getQuery()
	{
		return $this->query;
	}
}
