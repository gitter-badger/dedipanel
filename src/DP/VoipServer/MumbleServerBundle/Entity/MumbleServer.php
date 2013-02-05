<?php

namespace DP\VoipServer\MumbleServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DP\VoipServer\VoipServerBundle\Entity\VoipServer;
use DP\Core\MachineBundle\PHPSeclibWrapper\PHPSeclibWrapper;

/**
 * DP\MumbleServer\umbleServerBundle\Entity\MumbleServer
 * 
 * @ORM\Entity(repositoryClass="DP\MumbleServer\MumbleServerBundle\Entity\MumbleServerRepository")
 */
class MumbleServer extends VoipServer{
    
public function installServer(\Twig_Environment $twig)
    {
        // on définie les variables principales
        $installPath= $this->getAbsoluteDir(); 
        $logPath = $installPath . 'install.log';
        $scriptPath = $installPath . 'mumble_install.sh';     
        $mkdirCmd = 'if [ ! -e ' . $installPath . ' ]; then mkdir ' . $installPath . '; fi';
        
        $installScript = $twig->render(
            'DPMumbleServerBundle:sh:install_mumble.sh.twig', array('installDir' => $installPath));
        
        $screenCmd  = 'screen -dmS mumble ' . $scriptPath . '';
        
        // on éxecute les commandes
        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $sec->exec($mkdirCmd);
        $sec->upload($scriptPath, $installScript);
        $sec->exec($screenCmd);
        
        // on verifie que le log est présent et que l'install est bien fini :D
        $verification = $sec->exec($logPath);
        
        if ($verification == 'Install Ended') 
            $this->installationStatus = 1;
        else 
            $this->installationStatus = 0;
    }  
    
public function removeServer(\Twig_Environment $twig)
    {
        $installDir = $this->getDir();

        $rmCmd = 'if [ ! -e ' . $installDir . ' ]; then rm -f ' . $installDir . '; fi';
        
        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        $verification = $sec->exec($rmCmd);
        
        if(!$verification)
            return $this->installationStatus = 0;

    }
    public function uploadShellScripts(\Twig_Environment $twig)
    {        
        $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
        
        $scriptPath = $this->getAbsoluteHldsScriptPath();
        
        $mumbleScript = $twig->render(
            'DPMumbleServerBundle:sh:install_mumble.sh.twig', array());
        
        $sec->upload($mumbleScript, $hldsScript);
        
        $this->installationStatus = 2;
        
    }
        


}