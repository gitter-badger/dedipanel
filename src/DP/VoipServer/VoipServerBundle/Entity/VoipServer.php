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
use Symfony\Component\Validator\Constraints as Assert;
use DP\Core\MachineBundle\PHPSeclibWrapper\PHPSeclibWrapper;
use DP\Core\MachineBundle\Entity\Machine;

/**
 * DP\VoipServer\VoipServer\Entity\VoipServer
 *
 * @ORM\Table(name="voip_server")
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="servertype", type="string")
 * @ORM\DiscriminatorMap({"mumble" = "DP\VoipServer\MumbleServerBundle\Entity\Mumble"})
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
     * @ORM\ManyToOne(targetEntity="DP\Core\MachineBundle\Entity\Machine", inversedBy="voipServers")
     * @ORM\JoinColumn(name="machineId", referencedColumnName="id")
     * @Assert\NotNull(message="voipserver.assert.machine")
     */
    protected $machine;

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
     * Set machine
     *
     * @param \DP\Core\MachineBundle\Entity\Machine $machine
     * @return VoipServer
     */
    public function setMachine(\DP\Core\MachineBundle\Entity\Machine $machine = null)
    {
        $this->machine = $machine;
    
        return $this;
    }

    /**
     * Get machine
     *
     * @return \DP\Core\MachineBundle\Entity\Machine 
     */
    public function getMachine()
    {
        return $this->machine;
    }
}