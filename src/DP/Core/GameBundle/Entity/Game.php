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

namespace DP\Core\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DP\Core\GameBundle\Entity\Game
 * @author Albin Kerouanton 
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="DP\Core\GameBundle\Entity\GameRepository")
 */
class Game
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=32)
     * @Assert\NotBlank(message="game.assert.name")
     */
    private $name;

    /**
     * @var string $installName
     *
     * @ORM\Column(name="installName", type="string", length=24)
     * @Assert\NotBlank(message="game.assert.installName")
     */
    private $installName;

    /**
     * @var string $launchName
     *
     * @ORM\Column(name="launchName", type="string", length=24)
     * @Assert\NotBlank(message="game.assert.launchName")
     */
    private $launchName;

    /**
     * @var string $bin
     *
     * @ORM\Column(name="bin", type="string", length=24)
     */
    private $bin;

    /**
     * @var boolean $orangebox
     *
     * @ORM\Column(name="orangebox", type="boolean")
     */
    private $orangebox = false; // default value
    
    /**
     * @var boolean $source
     * 
     * @ORM\Column(name="source", type="boolean")
     */
    private $source = false; // default value, used for source tv

    /**
     * @var string $map
     *
     * @ORM\Column(name="map", type="string", length=20, nullable=true)
     */
    private $map;

    /**
     * @var boolean $available
     *
     * @ORM\Column(name="available", type="boolean")
     * @Assert\NotBlank(message="game.assert.available")
     */
    private $available = true;
    
    /**
     * @var string $binDir
     * 
     * @ORM\Column(name="binDir", type="string", length=20, nullable=true)
     */
    private $binDir;
    
    /**
     * @ORM\Column(name="sourceImagesMaps", type="string", length=255, nullable=true)
     * @var string
     */
    private $sourceImagesMaps;
    
    /** 
     * @var \Doctrine\Common\Collections\ArrayCollection $gameServers
     * 
     * @ORM\OneToMany(targetEntity="DP\GameServer\GameServerBundle\Entity\GameServer", mappedBy="game")
     */
    private $gameServers;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $plugins
     * 
     * @ORM\ManyToMany(targetEntity="DP\Core\GameBundle\Entity\Plugin", inversedBy="games")
     * @ORM\JoinTable(name="game_plugin", 
     *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")}, 
     *      inverseJoinColumns={@ORM\JoinColumn(name="plugin_id", referencedColumnName="id")}
     * )
     */
    private $plugins; 
    
    /**
     * @ORM\Column(name="type", type="string", length=32)
     * @Assert\Choice(choices={"steam", "minecraft"}, message="game.assert.type")
     */    
    private $type;
    
    /**
     * @ORM\Column(name="configTemplate", type="text", nullable=true)
     */
    private $configTemplate;
    

    public function __construct()
    {
        $this->plugins = new \Doctrine\Common\Collections\ArrayCollection(array());
    }
    
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set installName
     *
     * @param string $installName
     */
    public function setInstallName($installName)
    {
        $this->installName = $installName;
    }

    /**
     * Get installName
     *
     * @return string 
     */
    public function getInstallName()
    {
        return $this->installName;
    }

    /**
     * Set launchName
     *
     * @param string $launchName
     */
    public function setLaunchName($launchName)
    {
        $this->launchName = $launchName;
    }

    /**
     * Get launchName
     *
     * @return string 
     */
    public function getLaunchName()
    {
        return $this->launchName;
    }

    /**
     * Set bin
     *
     * @param string $bin
     */
    public function setBin($bin)
    {
        $this->bin = $bin;
    }

    /**
     * Get bin
     *
     * @return string 
     */
    public function getBin()
    {
        return $this->bin;
    }

    /**
     * Set orangebox
     *
     * @param boolean $orangebox
     */
    public function setOrangebox($orangebox)
    {
        $this->orangebox = $orangebox;
    }

    /**
     * Get orangebox
     *
     * @return boolean 
     */
    public function isOrangebox()
    {
        return $this->orangebox;
    }
    
    /**
     * Set source
     * 
     * @param boolean $source 
     */
    public function setSource($source)
    {
        $this->source = $source;
    }
    
    /**
     * Get source
     * 
     * @return boolean
     */
    public function isSource()
    {
        return $this->source;
    }

    /**
     * Set map
     *
     * @param string $map
     */
    public function setMap($map)
    {
        $this->map = $map;
    }

    /**
     * Get map
     *
     * @return string 
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Set available
     *
     * @param boolean $available
     */
    public function setAvailable($available)
    {
        $this->available = $available;
    }

    /**
     * Get available
     *
     * @return boolean 
     */
    public function getAvailable()
    {
        return $this->available;
    }
    
    public function __toString()
    {
        return $this->name;
    }
    
    /**
     * Set binary directory
     * 
     * @param string $binDir 
     */
    public function setBinDir($binDir)
    {
        $binDir = trim($binDir, '/') . '/';
        $this->binDir = $binDir;
    }
    
    /**
     * Get binary directory
     * 
     * @return string
     */
    public function getBinDir()
    {
        if ($this->isOrangebox()) {
            return 'orangebox/';
        }
        elseif (empty($this->binDir)) {
            return '';
        }
        else {
            return $this->binDir;
        }
    }
    
    /**
     * Set source of images maps
     * 
     * @param string $sourceImagesMaps 
     */
    public function setSourceImagesMaps($sourceImagesMaps)
    {
        $this->sourceImagesMaps = $sourceImagesMaps;
    }
    
    /**
     * Get source of images maps
     * 
     * @return string 
     */
    public function getSourceImagesMaps()
    {
        return $this->sourceImagesMaps;
    }
    
    /**
     * Add plugins
     * 
     * @param  $plugin \Doctrine\Common\Collections\ArrayCollection
     */
    public function addPlugin(\DP\Core\GameBundle\Entity\Plugin $plugin)
    {
        $this->plugins[] = $plugin;
        
        if (!$plugin->getGames()->contains($this)) {
            $plugin->addGame($this);
        }
    }
    
    public function removePlugin(Plugin $plugin)
    {
        $this->plugins->removeElement($plugin);
    }
    
    /**
     * Set plugin list
     * 
     * @param array $plugins 
     */
    public function setPlugins(array $plugins = array())
    {
        $this->plugins = new \Doctrine\Common\Collections\ArrayCollection($plugins);
    }
    
    /**
     * Get plugins
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
    
    /**
     * Set game type (steam or minecraft)
     * 
     * @param string $type 
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * @return string Game type
     */
    public function getType()
    {
        return $this->type;
    }
    
    public function isBukkit()
    {
        return $this->getInstallName() == 'bukkit';
    }
    
    /**
     * Set the server config file template
     * @param string|null $configTemplate 
     */
    public function setConfigTemplate($configTemplate)
    {
        $this->configTemplate = $configTemplate;
    }
    
    /**
     * Get the server config file template
     * 
     * @return string
     */
    public function getConfigTemplate()
    {
        return $this->configTemplate;
    }
}
