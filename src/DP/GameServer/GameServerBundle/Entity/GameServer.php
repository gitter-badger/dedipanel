<?php

/**
 * This file is part of Dedipanel project
 *
 * (c) 2010-2014 Dedipanel <http://www.dedicated-panel.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DP\GameServer\GameServerBundle\Entity;

use Dedipanel\PHPSeclibWrapperBundle\Connection\Exception\ScreenNotExistException;
use Doctrine\ORM\Mapping as ORM;
use DP\Core\MachineBundle\Entity\Machine;
use Symfony\Component\Validator\Constraints as Assert;
use DP\GameServer\GameServerBundle\Query\QueryInterface;
use DP\GameServer\GameServerBundle\Query\RconInterface;
use DP\Core\CoreBundle\Exception\NotImplementedException;
use DP\GameServer\GameServerBundle\FTP\AbstractItem;
use DP\GameServer\GameServerBundle\FTP\File;
use DP\GameServer\GameServerBundle\FTP\Directory;
use DP\Core\GameBundle\Entity\Plugin;
use DP\Core\CoreBundle\Model\AbstractServer;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * DP\Core\GameServer\GameServerBundle\Entity\GameServer
 * @ORM\Table(name="gameserver")
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *      "steam" = "DP\GameServer\SteamServerBundle\Entity\SteamServer",
 *      "minecraft" = "DP\GameServer\MinecraftServerBundle\Entity\MinecraftServer"
 * })
 */
abstract class GameServer extends AbstractServer
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=32)
     */
    protected $name;

    /**
     * @var integer $port
     *
     * @ORM\Column(name="port", type="integer")
     */
    protected $port;

    /**
     * @var integer $maxplayers
     *
     * @ORM\Column(name="maxplayers", type="integer")
     */
    protected $maxplayers;

    /**
     * @ORM\ManyToOne(targetEntity="DP\Core\MachineBundle\Entity\Machine", inversedBy="gameServers")
     * @ORM\JoinColumn(name="machineId", referencedColumnName="id")
     */
    protected $machine;

    /**
     * @ORM\ManyToOne(targetEntity="DP\Core\GameBundle\Entity\Game", inversedBy="gameServers")
     * @ORM\JoinColumn(name="gameId", referencedColumnName="id")
     */
    protected $game;

    /**
     * @var string $rcon
     *
     * @ORM\Column(name="rconPassword", type="string", length=32)
     */
    protected $rconPassword;

    protected $query;
    protected $rcon;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $plugins
     *
     * @ORM\ManyToMany(targetEntity="DP\Core\GameBundle\Entity\Plugin")
     * @ORM\JoinTable(name="gameserver_plugins",
     *      joinColumns={@ORM\JoinColumn(name="server_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="plugin_id", referencedColumnName="id")}
     * )
     */
    private $plugins;


    public function __construct()
    {
        $this->plugins = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set port
     *
     * @param integer $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Get port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set game
     *
     * @param Game $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }

    /**
     * Get gameId
     *
     * @return integer
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set maxplayers
     *
     * @param integer $maxplayers
     */
    public function setMaxplayers($maxplayers)
    {
        $this->maxplayers = $maxplayers;
    }

    /**
     * Get maxplayers
     *
     * @return integer
     */
    public function getMaxplayers()
    {
        return $this->maxplayers;
    }

    /**
     * Get absolute path of binaries directory
     *
     * @return string
     */
    public function getAbsoluteBinDir()
    {
        return rtrim(rtrim($this->getAbsoluteDir(), '/') . '/' . $this->getGame()->getBinDir() . '/', '/') . '/';
    }

    /**
     * Get absolute path of game content directory
     *
     * @return string
     */
    public function getAbsoluteGameContentDir()
    {
        return $this->getAbsoluteBinDir();
    }

    public function getScreenName()
    {
        $screenName = $this->getMachine()->getUsername() . '-' . $this->getDir();

        return $this->getScreenNameHash($screenName);
    }

    public function getInstallScreenName()
    {
        $screenName = $this->getMachine()->getUsername() . '-install-' . $this->getDir();

        return $this->getScreenNameHash($screenName);
    }

    public function getPluginInstallScreenName($scriptName = '')
    {
        $screenName = $this->getMachine()->getUsername() . '-plugin-install-' . $scriptName . '-' . $this->getDir();

        return $this->getScreenNameHash($screenName);
    }

    public function getScreenNameHash($screenName, $hashLength = 20)
    {
        $screenName = sha1($screenName);
        $screenName = substr($screenName, 0, $hashLength);

        return 'dp-' . $screenName;
    }

    public function setQuery(QueryInterface $query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set rconPassword
     *
     * @param string $rconPassword
     */
    public function setRconPassword($rconPassword)
    {
        $this->rconPassword = $rconPassword;
    }

    /**
     * Get rconPassword
     *
     * @return string
     */
    public function getRconPassword()
    {
        return $this->rconPassword;
    }

    public function isEmptyRconPassword()
    {
        return empty($this->rconPassword);
    }

    public function setRcon(RconInterface $rcon)
    {
        $this->rcon = $rcon;

        return $this->rcon;
    }

    public function getRcon()
    {
        return $this->rcon;
    }

    /**
     * Add plugin
     *
     * @param \DP\Core\GameBundle\Entity\Plugin $plugin
     */
    public function addPlugin(\DP\Core\GameBundle\Entity\Plugin $plugin)
    {
        $this->plugins[] = $plugin;
    }

    /**
     * Remove a server plugin
     * @param \DP\Core\GameBundle\Entity\Plugin $plugin
     */
    public function removePlugin(\DP\Core\GameBundle\Entity\Plugin $plugin)
    {
        $this->plugins->removeElement($plugin);
    }

    /**
     * Get plugins recorded as "installed on the server"
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPlugins()
    {
        if ($this->plugins instanceof \Doctrine\ORM\PersistentCollection) {
            return $this->plugins->getValues();
        }
        else {
            return $this->plugins;
        }
    }

    public function getInstalledPlugins()
    {
        return $this->getPlugins();
    }

    public function getNotInstalledPlugins()
    {
        $intersectCallback = function ($plugin1, $plugin2) {
            return $plugin1->getId() - $plugin2->getId();
        };
        $plugins = $this->getGame()->getPlugins()->getValues();

        // On compare l'array contenant l'ensemble des plugins dispo pour le jeu
        // A ceux installés sur le serveur
        return array_udiff($plugins, $this->getPlugins(), $intersectCallback);
    }
    
    public function getServerLogs()
    {
        try {
            return $this->getMachine()->getConnection()->getScreenContent($this->getScreenName());
        }
        catch (ScreenNotExistException $e) {
            return null;
        }
    }
    
    public function getInstallLogs()
    {
        try {
            return $this->getMachine()->getConnection()->getScreenContent($this->getInstallScreenName());
        }
        catch (ScreenNotExistException $e) {
            return null;
        }
    }
    
    public function finalizeInstallation(\Twig_Environment $twig)
    {
        $this->uploadShellScripts($twig);
        $this->uploadDefaultServerConfigurationFile();
        $this->removeInstallationFiles();
        
        $this->setInstallationStatus(101);

        return true;
    }
    
    /**
     * @todo: refacto domain logic
     */
    public function installPlugin(\Twig_Environment $twig, Plugin $plugin)
    {
        throw new NotImplementedException();
    }
    
    public function uninstallPlugin(\Twig_Environment $twig, Plugin $plugin)
    {
        throw new NotImplementedException();
    }
    
    abstract public function uploadShellScripts(\Twig_Environment $twig);
    
    abstract public function uploadDefaultServerConfigurationFile();
    
    abstract public function removeInstallationFiles();
    
    abstract public function regenerateScripts(\Twig_Environment $twig);

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('machine', new Assert\NotNull(array('message' => 'gameServer.assert.machine')));
        $metadata->addPropertyConstraint('name', new Assert\NotBlank(array('message' => 'gameServer.assert.name')));
        $metadata->addPropertyConstraint('port', new Assert\NotBlank(array('message' => 'gameServer.assert.port')));
        $metadata->addPropertyConstraint('port', new Assert\Range(array(
            'min' => 1024, 'minMessage' => 'gameServer.assert.port',
            'max' => 65536, 'maxMessage' => 'gameServer.assert.port'
        )));
        $metadata->addPropertyConstraint('rconPassword', new Assert\NotBlank(array('message' => 'gameServer.assert.rconPassword')));
        $metadata->addPropertyConstraint('dir', new Assert\NotBlank(array('message' => 'gameServer.assert.dir')));
        $metadata->addPropertyConstraint('maxplayers', new Assert\NotBlank(array('message' => 'gameServer.assert.maxplayers')));
        $metadata->addPropertyConstraint('maxplayers', new Assert\Range(array('min' => 2, 'minMessage' => 'gameServer.assert.maxplayers')));
    }
}
