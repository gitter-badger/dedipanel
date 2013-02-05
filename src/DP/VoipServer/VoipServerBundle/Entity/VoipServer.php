<?php

/*
** Copyright (C) 2010-2012 Kerouanton Albin, Smedts Jérôme
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along
** with this program; if not, write to the Free Software Foundation, Inc.,
** 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace DP\VoipServer\VoipServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DP\Core\MachineBundle\Entity\Machine;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DP\VoipServer\VoipServer\Entity\VoipServer
 *
 * @ORM\Table(name="voipserver")
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="servertype", type="string")
 * @ORM\DiscriminatorMap({"mumble" = "DP\VoipServer\MumbleServerBundle\Entity\MumbleServer"})
 */
class VoipServer {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer $portMumble
     *
     * @ORM\Column(name="portMumble", type="integer")
     * @Assert\Min(limit=1, message="voipserver.assert.port")
     * @Assert\Max(limit=65536, message="voipserver.assert.port")
     */
    private $portMumble = 6502;

    /**
     * @var string $dir
     *
     * @ORM\Column(name="dir", type="string", length=64)
     * @Assert\NotBlank(message="mumbleServer.assert.dir")
     */
    protected $dir = 'mumble';
    
    /**
     * @var string $repertoire
     *
     * @ORM\Column(name="repertoire", type="string", length=32, nullable=true)
     */
    private $iceSecret;
    
    /**
     * @ORM\ManyToOne(targetEntity="DP\Core\MachineBundle\Entity\Machine", inversedBy="voipserver")
     * @ORM\JoinColumn(name="machineId", referencedColumnName="id")
     * @Assert\NotNull(message="voipserver.assert.machine")
     */
    protected $machine;

    /**
     * @var integer $installationStatus
     *
     * @ORM\Column(name="installationStatus", type="integer", nullable=true)
     */
    protected $installationStatus;
    
    
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
     * Set portMumble
     *
     * @param integer $portMumble
     * @return VoipServer
     */
    public function setPortMumble($portMumble)
    {
        $this->portMumble = $portMumble;
    
        return $this;
    }

    /**
     * Get portMumble
     *
     * @return integer 
     */
    public function getPortMumble()
    {
        return $this->portMumble;
    }

     /**
     * Set machine
     *
     * @param Machine $machine
     */
    public function setMachine(Machine $machine)
    {
        $this->machine = $machine;
    }

    /**
     * Get machine
     *
     * @return Machine
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * Set dir
     *
     * @param string $dir
     * @return VoipServer
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    
        return $this;
    }

    /**
     * Get dir
     *
     * @return string 
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Set iceSecret
     *
     * @param string $iceSecret
     * @return VoipServer
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
     * Get absolute path of server installation directory
     * 
     * @return string
     */
    public function getAbsoluteDir()
    {
        return $this->getMachine()->getHome() . '/' . $this->getDir() . '/';
    }

    /**
     * Set installationStatus
     *
     * @param integer $installationStatus
     * @return VoipServer
     */
    public function setInstallationStatus($installationStatus)
    {
        $this->installationStatus = $installationStatus;
    
        return $this;
    }

    /**
     * Get installationStatus
     *
     * @return integer 
     */
    public function getInstallationStatus()
    {
        return $this->installationStatus;
    }
}