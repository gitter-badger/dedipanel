<?php

namespace DP\VoipServer\MumbleServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DP\VoipServer\VoipServerBundle\Entity\VoipServerEntity;

/**
 * DP\VoipServer\MumbleServerBundle\Entity\MumbleServerEntity
 *
 * @ORM\Table(name="mumble_server")
 * @ORM\Entity(repositoryClass="DP\VoipServer\MumbleServerBundle\Entity\MumbleServerEntityRepository")
 */
class MumbleServerEntity extends VoipServerEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
