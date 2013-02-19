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
    /*
     * 
     */
    public function installServer(\Twig_Environment $twig)
        {
            // on définie les variables principales
            $installPath = $this->getAbsoluteDir(); 

            // on définie les commandes shell
            $scriptPath = $installPath . 'mumble_install.sh';     
            $mkdirCmd  = 'if [ -e  ' . $installPath . ' ]; then ';
            $mkdirCmd .= 'rm -rf  ' . $installPath . '; ';
            $mkdirCmd .= 'else mkdir ' . $installPath . '; fi;';

            $screenCmd  = 'screen -dmS mumble ' . $scriptPath .'';
 
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
            $logCmd = 'if [ -f ' . $logPath . ' ]; then cat ' . $logPath . '; else echo "File not found exception."; fi; ';

            // on verifie que le log est présent et que l'install est bien fini :D
            $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
            $installLog = $sec->exec($logCmd);
            
            if($installLog != 'File not found exception.'){
                if (strpos($installLog, 'Shell upload ended') !== false){                
                    return 2;
                }
                elseif(strpos($installLog, 'Install ended') !== false ){
                    return 1;
                }
                elseif(strpos($installLog, 'Install aborded')  !== false or
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
    /*
     *  Uplod Shell script to start mumble or stop it
     */    
    public function uploadShellScript(\Twig_Environment $twig)
        {        
            // on définie les variables principales
            $installPath = $this->getAbsoluteDir();
            $screenName = $this->getDir();
            $scriptPath = $this->getAbsoluteScriptPath();
            $log = $installPath . 'install.log';
            
            // on définie les commandes shell
            $shelldExist =  'if [ -f ' . $scriptPath . ' ]; then ';
            $shelldExist .= 'echo "Shell upload ended" >> ' . $log . '; ';
            $shelldExist .= 'else echo "Shell upload aborded" >> ' . $log . '; fi;';
            
            $mumbleScript = $twig->render(
                 'DPMumbleServerBundle:sh:mumble.sh.twig', array(
                 'screenName' => $screenName,
                 'binDir' => $installPath
             ));

             $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
             $log = $sec->upload($scriptPath, $mumbleScript, 0750);
             $sec->exec($shelldExist);
        }
    /*
     * Change the server
     * $state = start, stop or restart.
     */    
    public function changeStateServer($state)
        {
            return PHPSeclibWrapper::getFromMachineEntity($this->getMachine())
                    ->exec($this->getAbsoluteScriptPath() . ' ' . $state);
        }
        
    public function getAbsoluteScriptPath()
    {
        return $this->getAbsoluteDir() . 'mumble.sh';
    }    

}