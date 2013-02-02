<?php

namespace DP\VoipServer\MumbleServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DP\VoipServer\VoipServerBundle\Entity\VoipServer;

/**
 * DP\MumbleServer\umbleServerBundle\Entity\MumbleServer
 * 
 * @ORM\Entity(repositoryClass="DP\MumbleServer\MumbleServerBundle\Entity\MumbleServerRepository")
 */
class MumbleServer extends VoipServer{
    
public function installServer(\Twig_Environment $twig)
    {
        $installDir = $this->getDir();
        
        $logPath = $installDir . 'install.log';
        $mkdirCmd = 'if [ ! -e ' . $installDir . ' ]; then mkdir ' . $installDir . '; fi';
        $dlUrl = 'http://freefr.dl.sourceforge.net/project/mumble/Mumble/1.2.3/murmur-static_x86-1.2.3.tar.bz2';
        
        $dlCmd = 'cd ' . $installDir . ' && wget -N -o ' . $logPath . ' ' . $dlUrl . ' &';
        
        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $sec->exec($mkdirCmd);
        $sec->exec($dlCmd);
        
        $this->installationStatus = 0;
    }  
    
public function removeServer(\Twig_Environment $twig)
    {
        $installDir = $this->getDir();

        $dlCmd = 'rm -f ' . $installDir . '';
        
        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $sec->exec($dlCmd);
        
        $this->installationStatus = 0;
    }  

}