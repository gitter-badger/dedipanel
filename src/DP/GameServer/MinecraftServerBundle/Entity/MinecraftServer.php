<?php

/**
 * This file is part of Dedipanel project
 *
 * (c) 2010-2014 Dedipanel <http://www.dedicated-panel.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DP\GameServer\MinecraftServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DP\GameServer\GameServerBundle\Entity\GameServer;
use Symfony\Component\Validator\Constraints as Assert;
use DP\Core\MachineBundle\PHPSeclibWrapper\PHPSeclibWrapper;
use DP\Core\GameBundle\Entity\Plugin;
use DP\Core\CoreBundle\Exception\MissingPacketException;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * DP\GameServer\MinecraftServerBundle\Entity\MinecraftServer
 *
 * @ORM\Table(name="minecraft_server")
 * @ORM\Entity(repositoryClass="DP\GameServer\GameServerBundle\Entity\GameServerRepository")
 * 
 * @todo: refacto phpseclib
 * @todo: refacto domain logic
 */
class MinecraftServer extends GameServer
{

    /**
     * @var integer $queryPort
     *
     * @ORM\Column(name="queryPort", type="integer", nullable=true)
     */
    protected $queryPort;

    /**
     * @var integer $rconPort
     *
     * @ORM\Column(name="rconPort", type="integer")
     */
    protected $rconPort;

    /**
     * @var integer $minHeap
     *
     * @ORM\Column(name="minHeap", type="integer")
     */
    protected $minHeap;

    /**
     * @var integer $maxHeap
     *
     * @ORM\Column(name="maxHeap", type="integer")
     */
    protected $maxHeap;

    /*
     * Set minecraft query port
     *
     * @param integer $queryPort
     */
    public function setQueryPort($queryPort)
    {
        $this->queryPort = $queryPort;
    }

    /*
     * Get minecraft query port
     *
     * @return integer Query port
     */
    public function getQueryPort()
    {
        return $this->queryPort;
    }

    /*
     * Set rcon port
     *
     * @param integer $rconPort
     */
    public function setRconPort($rconPort)
    {
        $this->rconPort = $rconPort;
    }

    /*
     * Get rcon port
     *
     * @return integer RCON Port
     */
    public function getRconPort()
    {
        return $this->rconPort;
    }

    /**
     * Set min heap
     *
     * @param integer $minHeap
     */
    public function setMinHeap($minHeap)
    {
        $this->minHeap = $minHeap;
    }

    /**
     * Get min heap
     *
     * @return integer Min heap
     */
    public function getMinHeap()
    {
        return $this->minHeap;
    }

    /**
     * Set max heap
     *
     * @param integer $maxHeap
     */
    public function setMaxHeap($maxHeap)
    {
        $this->maxHeap = $maxHeap;
    }

    /**
     * Get max heap
     *
     * @return integer Max heap
     */
    public function getMaxHeap()
    {
        return $this->maxHeap;
    }

    /**
     * Download server
     */
    public function installServer(\Twig_Environment $twig)
    {
        $conn = $this->getMachine()->getConnection();

        if (!$conn->isJavaInstalled()) {
            throw new MissingPacketException('oracle-java8-installer');
        }

        $installDir = $this->getAbsoluteDir();
        $logPath = $installDir . 'install.log';

        $conn->mkdir($installDir);

        $dlUrl = 'https://s3.amazonaws.com/MinecraftDownload/launcher/minecraft_server.jar';
        if ($this->game->getInstallName() == 'bukkit') {
            $dlUrl = 'http://dl.bukkit.org/latest-rb/craftbukkit.jar';
        }

        $conn->exec('cd ' . $installDir . ' && wget -N -o ' . $logPath . ' ' . $dlUrl . ' &');

        $this->installationStatus = 0;

        return true;
    }

    public function getInstallationProgress()
    {
        $installDir = $this->getAbsoluteDir();
        $logPath = $installDir . 'install.log';
        $binPath = $installDir . $this->getGame()->getBin();
        $conn    = $this->getMachine()->getConnection();
        
        $status = $conn->exec("if [ -d $installDir ]; then if [ -e $logPath ]; then echo 1; elif [ -e $binPath ]; then echo 2; else echo 0; fi; else echo 0; fi;");

        if ($status == 2) {
            return 100;
        }

        // On récupère les 20 dernières lignes du fichier afin de déterminer le pourcentage
        $installLog = $conn->exec('tail -n 20 ' . $logPath);
        $percent    = $this->getPercentFromInstallLog($installLog);

        // Suppression du fichier de log si le dl est terminé
        if (!empty($percent) && $percent == 100) {
            $conn->exec('rm ' . $logPath);
        }

        return $percent;
    }

    public function uploadShellScripts(\Twig_Environment $twig)
    {
        $conn = $this->getMachine()->getConnection();
        $game = $this->getGame();

        $scriptPath = $this->getAbsoluteDir() . 'minecraft.sh';

        $minecraftScript = $twig->render('DPMinecraftServerBundle:sh:minecraft.sh.twig', array(
            'screenName' => $this->getScreenName(), 'bin' => $game->getBin(),
            'options' => 'nogui', 'minHeap' => $this->getMinHeap(), 'maxHeap' => $this->getMaxHeap(),
            'parallelThreads' => 1, 'binDir' => $this->getAbsoluteBinDir(), 'core' => implode(',', $this->getCore()),
        ));

        if (!$conn->upload($scriptPath, $minecraftScript, 0750)) {
            return false;
        }

        $this->installationStatus = 101;

        return true;
    }
    
    public function regenerateScripts(\Twig_Environment $twig)
    {
        return $this->uploadShellScripts($twig);
    }

    /**
     * {@inheritdoc}
     */
    public function changeState($state)
    {
        $scriptPath = $this->getAbsoluteDir() . 'minecraft.sh';

        return $this->getMachine()->getConnection()->exec($scriptPath . ' ' . $state);
    }

    public function uploadDefaultServerConfigurationFile()
    {
        $template = $this->getGame()->getConfigTemplate();

        if (!empty($template)) {
            $conn = $this->getMachine()->getConnection();
            $cfgPath = $this->getAbsoluteDir() . 'server.properties';

            // Supression du fichier s'il existe déjà
            $conn->exec('if [ -e ' . $cfgPath . ']; then rm ' . $cfgPath . '; fi');

            $env = new \Twig_Environment(new \Twig_Loader_String());
            $cfgFile = $env->render($template, array(
                'serverPort'    => $this->getPort(),
                'queryPort'     => $this->getQueryPort(),
                'rconPort'      => $this->getRconPort(),
                'rconPassword'  => $this->getRconPassword(),
                'maxPlayers'    => $this->getMaxplayers(),
                'motd'          => $this->getFullName(),
                'ip'            => $this->getMachine()->getPublicIp(),
            ));

            return $conn->upload($cfgPath, $cfgFile, 0750);
        }

        return false;
    }

    public function modifyServerPropertiesFile()
    {
        // Variables à modifier dans le fichier server.properties
        $varToChange = array(
            'motd'          => $this->getName(), 
            'server-port'   => $this->getPort(),
            'enable-query'  => 'true',
            'query.port'    => $this->getQueryPort(),
            'enable-rcon'   => 'true',
            'rcon.port'     => $this->getRconPort(),
            'rcon.password' => $this->getRconPassword(),
            'server-ip'     => $this->getMachine()->getPublicIp(),
            'max-players'   => $this->getMaxplayers(),
        );

        // Récupération du fichier server.properties distant
        $conn = $this->getMachine()->getConnection();
        $cfgPath = $this->getAbsoluteDir() . 'server.properties';

        $remoteFile = $conn->download($cfgPath);
        $fileLines = explode("\n", $remoteFile);

        foreach ($fileLines AS &$line) {
            if (false === $pos = strpos($line, '=')) continue;

            // Extraction du nom de la variable
            $var = substr($line, 0, $pos);

            // Si c'est l'une des variables à modifier, on modifie la ligne
            // Et on supprime l'entrée dans l'array des variables à modifier
            if (array_key_exists($var, $varToChange)) {
                $line = $var . '=' . $varToChange[$var];

                unset($varToChange[$var]);
            }
        }
        // Suppression de la référence
        unset($line);

        // S'il reste des variables dans l'array $varToChange
        // On ajoute les lignes au fichier
        // (puisqu'elle n'existe pas, les nouvelles valeurs n'ont pas encore été mises)
        if (!empty($varToChange)) {
            foreach ($varToChange AS $var => $val) {
                $fileLines[] .= $var . '=' . $val;
            }
        }

        // Upload du nouveau fichier
        return $conn->upload($cfgPath, implode("\n", $fileLines));
    }

    public function execPluginScript(\Twig_Environment $twig, Plugin $plugin, $action)
    {
        if ($action != 'install' && $action != 'uninstall') {
            throw new BadMethodCallException('Only actions available for MinecraftServers plugin script are : install and uninstall.');
        }
        
        $conn = $this->getMachine()->getConnection();
        
        $dir = $this->getAbsoluteDir();
        $scriptPath = $dir . 'plugin.sh';
        $pluginScript = $twig->render('DPMinecraftServerBundle:sh:plugin.sh.twig', array('gameDir' => $dir . 'plugins'));
            
        $conn->upload($scriptPath, $pluginScript);
        
        $screenName = $this->getPluginInstallScreenName();
        $screenCmd  = 'screen -dmS ' . $screenName . ' ' . $scriptPath . ' ' . $action;
        
        if ($action == 'install') {
            $screenCmd .= ' "' . $plugin->getDownloadUrl () . '"';
        }
        
        $conn->exec($screenCmd);
    }

    public function removeFromServer()
    {
        $screenName = $this->getScreenName();
        $serverDir = $this->getAbsoluteDir();
        $scriptPath = $serverDir . 'minecraft.sh';
        
        $conn = $this->getMachine()->getConnection();

        // On commence par vérifier que le serveur n'est pas lancé (sinon on l'arrête)
        $pgrep   = '`ps aux | grep SCREEN | grep "' . $screenName . ' " | grep -v grep | wc -l`';
        $stopCmd = 'if [ ' . $pgrep . ' != "0" ]; then ' . $scriptPath . ' stop; fi;';
        $conn->exec($stopCmd);

        // Puis on supprime complètement le dossier du serveur
        return $conn->remove($serverDir);
    }

    public function removeInstallationFiles()
    {
        $logPath = $this->getAbsoluteDir() . 'install.log';

        return $this->getMachine()->getConnection()->remove($logPath);
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('queryPort', new Assert\NotBlank(array('message' => 'minecraft.assert.query_port.empty')));
        $metadata->addPropertyConstraint('queryPort', new Assert\Range(array(
            'min' => 1024,
            'minMessage' => 'minecraft.assert.query_port.min',
            'max' => 65536,
            'maxMessage' => 'minecraft.assert.query_port.max'
        )));
        $metadata->addPropertyConstraint('rconPort', new Assert\NotBlank(array('message' => 'minecraft.assert.rcon_port.empty')));
        $metadata->addPropertyConstraint('rconPort', new Assert\Range(array(
            'min' => 1024,
            'minMessage' => 'minecraft.assert.rcon_port.min',
            'max' => 65536,
            'maxMessage' => 'minecraft.assert.rcon_port.max'
        )));
        $metadata->addPropertyConstraint('minHeap', new Assert\NotBlank(array('message' => 'minecraft.assert.min_heap')));
        $metadata->addPropertyConstraint('maxHeap', new Assert\NotBlank(array('message' => 'minecraft.assert.max_heap')));
    }
}
