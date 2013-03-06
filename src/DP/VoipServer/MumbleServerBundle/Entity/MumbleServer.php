<?php

namespace DP\VoipServer\MumbleServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DP\VoipServer\VoipServerBundle\Entity\VoipServer;
use DP\Core\MachineBundle\PHPSeclibWrapper\PHPSeclibWrapper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DP\MumbleServer\umbleServerBundle\Entity\MumbleServer
 * 
 * @ORM\Table(name="mumble_server")
 * @ORM\Entity(repositoryClass="DP\MumbleServer\MumbleServerBundle\Entity\MumbleServerRepository")
 */
class MumbleServer extends VoipServer {
    /**
     * @var string serverName
     *
     * @ORM\Column(name="serverName", type="string", length=32, nullable=true)
     */
    private $serverName = '[Dedicated-Panel]';
    /**
     * @var string serverPassword
     *
     * @ORM\Column(name="serverPassword", type="integer", nullable=true)
     */
    private $serverPassword;
        /**
     * @var integer serverPassword
     *
     * @ORM\Column(name="maxUser", type="integer", nullable=true)
     */
    private $maxUser = 100;
    /**
     * @var integer portIce
     *
     * @ORM\Column(name="portIce", type="integer")
     * @Assert\Min(limit=1, message="voipserver.assert.port")
     * @Assert\Max(limit=65536, message="voipserver.assert.port")
     */
    private $portIce = 6502;
    /**
     * @var integer $portMumble
     *
     * @ORM\Column(name="portMumble", type="integer")
     * @Assert\Min(limit=1, message="voipserver.assert.port")
     * @Assert\Max(limit=65536, message="voipserver.assert.port")
     */
    private $portMumble = 64738;
    /**
     * @var string iceSecret
     *
     * @ORM\Column(name="iceSecret", type="string", length=32, nullable=true)
     */
    private $iceSecret;
    /**
     * @var string dir
     *
     * @ORM\Column(name="dir", type="string", length=64)
     * @Assert\NotBlank(message="mumbleServer.assert.dir")
     */
    protected $dir = 'mumble';
    /**
     * @var string logName
     *
     * @ORM\Column(name="logName", type="string", length=16, nullable=true)
     */
    private $logName = 'mumble-log';
    /**
     * @var integer logDuration
     *
     * @ORM\Column(name="logDuration", type="integer", nullable=true)
     */
    private $logDuration = 31;
    /**
     * @var string welcomeText
     *
     * @ORM\Column(name="welcomeText", type="string", length=250, nullable=true)
     * @Assert\Min(limit=0, message="voipserver.assert.port")
     * @Assert\Max(limit=31, message="voipserver.assert.port")
     */
    private $welcomeText;
    /**
     * @var integer bandWidth
     *
     * @ORM\Column(name="bandWidth", type="integer", nullable=true)
     * @Assert\Min(limit=0, message="voipserver.assert.port")
     * @Assert\Max(limit=100000, message="voipserver.assert.port")
     */
    private $bandWidth = 100000;

    /**
     * Set serverName
     *
     * @param string $serverName
     * @return MumbleServer
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;
    
        return $this;
    }

    /**
     * Get serverName
     *
     * @return string 
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * Set serverPassword
     *
     * @param integer $serverPassword
     * @return MumbleServer
     */
    public function setServerPassword($serverPassword)
    {
        $this->serverPassword = $serverPassword;
    
        return $this;
    }

    /**
     * Get serverPassword
     *
     * @return integer 
     */
    public function getServerPassword()
    {
        return $this->serverPassword;
    }

    /**
     * Set maxUser
     *
     * @param integer $maxUser
     * @return MumbleServer
     */
    public function setMaxUser($maxUser)
    {
        $this->maxUser = $maxUser;
    
        return $this;
    }

    /**
     * Get maxUser
     *
     * @return integer 
     */
    public function getMaxUser()
    {
        return $this->maxUser;
    }

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
     * Set portMumble
     *
     * @param integer $portMumble
     * @return MumbleServer
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
     * Set dir
     *
     * @param string $dir
     * @return MumbleServer
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
     * Set logName
     *
     * @param string $logName
     * @return MumbleServer
     */
    public function setLogName($logName)
    {
        $this->logName = $logName;
    
        return $this;
    }

    /**
     * Get logName
     *
     * @return string 
     */
    public function getLogName()
    {
        return $this->logName;
    }

    /**
     * Set logDuration
     *
     * @param integer $logDuration
     * @return MumbleServer
     */
    public function setLogDuration($logDuration)
    {
        $this->logDuration = $logDuration;
    
        return $this;
    }

    /**
     * Get logDuration
     *
     * @return integer 
     */
    public function getLogDuration()
    {
        return $this->logDuration;
    }

    /**
     * Set welcomeText
     *
     * @param string $welcomeText
     * @return MumbleServer
     */
    public function setWelcomeText($welcomeText)
    {
        $this->welcomeText = $welcomeText;
    
        return $this;
    }

    /**
     * Get welcomeText
     *
     * @return string 
     */
    public function getWelcomeText()
    {
        return $this->welcomeText;
    }

    /**
     * Set bandWidth
     *
     * @param integer $bandWidth
     * @return MumbleServer
     */
    public function setBandWidth($bandWidth)
    {
        $this->bandWidth = $bandWidth;
    
        return $this;
    }

    /**
     * Get bandWidth
     *
     * @return integer 
     */
    public function getBandWidth()
    {
        return $this->bandWidth;
    }
    /*
     * 
     */
    public function installServer(\Twig_Environment $twig) {
        // on définie les variables principales
        $installPath = $this->getAbsoluteDir();

        // on définie les commandes shell
        $scriptPath = $installPath . 'mumble_install.sh';
        $mkdirCmd  = 'if [ -e  ' . $installPath . ' ]; then ';
        $mkdirCmd .= 'rm -rf  ' . $installPath . '; ';
        $mkdirCmd .= 'else mkdir ' . $installPath . '; fi;';

        $screenCmd = 'screen -dmS mumble ' . $scriptPath . '';

        $installScript = $twig->render(
                'DPMumbleServerBundle:sh:install_mumble.sh.twig', array('installDir' => $installPath));

        // on éxecute les commandes shell
        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $sec->exec($mkdirCmd);
        $sec->upload($scriptPath, $installScript);
        $sec->exec($screenCmd);

        return 1;
    }
    /*
     * Verification if the server is correctly installed
     * If it's not the case, the status is update.
     * 
     * status = 0 Installation is stopped due to a bug
     * status = 1 Installation is suspended
     * status = 2 Mumble is correctly installed
     */
    public function verificationServer() 
    {
        // on définie les variables principales
        $installPath = $this->getAbsoluteDir();
        $logPath = $installPath . 'install.log';
        $logCmd  = 'if [ -f ' . $logPath . ' ]; then '; 
        $logCmd .= 'cat ' . $logPath . '; '; 
        $logCmd .= 'else echo "File not found exception."; fi;';
        
        // on verifie que le log est présent et que l'install est bien fini :D
        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $installLog = $sec->exec($logCmd);
        
        if ($installLog != 'File not found exception.') {
            if (strpos($installLog, 'Shell upload ended') !== false) {
                return 2;
            } elseif (strpos($installLog, 'Install ended') !== false) {
                return 1;
            } elseif (strpos($installLog, 'Install aborded') !== false or
                    strpos($installLog, 'Shell upload aborded') !== false) {
                return 0;
            }
        }

        return 0;
    }
    /*
     * Delete the path and all of the mumble server files
     */
    public function removeServer() 
    {
        $installDir = $this->getAbsoluteDir();
        $rmCmd = 'if [ -d ' . $installDir . ' ]; then rm -rf ' . $installDir . '; fi';

        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $sec->exec($rmCmd);

        return 0;
    }
    public function uploadShellScripts(\Twig_Environment $twig) 
    {         
        $scriptStatePath = $this->getAbsoluteStatePath();
        $installPath = $this->getAbsoluteDir();
        $log = $installPath . 'install.log';
        
        // Upload du script du script start/stop/restart
        $this->uploadStateScript($twig);

        // Upload du script du mumbl.ini modifier poru le panel
         $this->uploadIniScript($twig);

        // on définie les commandes shell
        $shelldExist = 'if [ -f ' . $scriptStatePath . ' ]; then ';
        $shelldExist .= 'echo "Shell upload ended" >> ' . $log . '; ';
        $shelldExist .= 'else echo "Shell upload aborded" >> ' . $log . '; fi;';

        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $sec->exec($shelldExist);
    }
    /*
     *  Uplod Shell script to change state of mumble and change the configuration
     */
    public function uploadStateScript(\Twig_Environment $twig) 
    {       
        $installPath = $this->getAbsoluteDir();
        $scriptPath = $this->getAbsoluteStatePath();
        
        // On regarde si le mumble.sh existe, si c'est le cas on le supprime
        $shelldExist  = 'if [ -e ' . $scriptPath . ' ]; then rm -f  ' . $scriptPath . '; fi';
                
        $mumbleScript = $twig->render(
            'DPMumbleServerBundle:sh:mumble.sh.twig', array(
            'binDir' => $installPath
            ));
        
        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $sec->exec($shelldExist);
        $sec->upload($scriptPath, $mumbleScript, 0750);
    }
    /*
     *  Uplod Shell script to start mumble or stop it
     */
    public function uploadIniScript(\Twig_Environment $twig) 
    {
        $installPath = $this->getAbsoluteDir();
        $scriptPath = $installPath . 'murmur.ini';
        
        // On regarde si le mumble.ini existe, si c'est le cas on le supprime pour mettre le notre
        $shelldExist  = 'if [ -e ' . $scriptPath . ' ]; then rm -f  ' . $scriptPath . '; fi';

        $mumbleScript = $twig->render(
            'DPMumbleServerBundle:sh:murmur.ini.twig', array(
            'ip' => $this->getMachine()->getPrivateIp(),
            'portIce' => $this->getPortIce(),
            'iceSecret' => $this->geticesecret(),
            'binDir' => $installPath,
            'welcomeText' => $this->getWelcomeText(),
            'portMumble' => $this->getPortMumble(),
            'bandWidth' => $this->getBandWidth(),
            'maxUser' => $this->getMaxUser(),
            'logName' => $this->getLogName(),
            'logDuration' => $this->getLogDuration(),
            'serverName' => $this->getServerName(),
            'serverPassword' => $this->getServerPassword(),
            ));

        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $sec->exec($shelldExist);
        $sec->upload($scriptPath, $mumbleScript, 0750);
    }
    /*
     * Change the server
     * $state = start, stop or restart.
     */
    public function changeStateServer($state) 
    {
        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $status = $sec->exec($this->getAbsoluteStatePath() . ' ' . $state);
        if ($status == 'serveur start') {
            return 1;
        } elseif ($status == 'Serveur already started') {
            return 1;
        } elseif ($status == 'serveur stop') {
            return 0;
        } elseif ($status == 'Serveur already stoped') {
            return 0;
        }
    }
    public function getAbsoluteDir()
    {
        return $this->getMachine()->getHome() . '/' . $this->getDir() . '/';
    }
    public function getAbsoluteStatePath() 
    {
        return $this->getAbsoluteDir() . 'mumble.sh';
    }
}