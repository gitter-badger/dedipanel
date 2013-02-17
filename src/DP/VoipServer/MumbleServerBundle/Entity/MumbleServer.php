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
            $screenName = $this->getDir();
            $installPath = $this->getAbsoluteDir(); 
            
            // on définie les commandes shell
            $scriptPath = $installPath . 'mumble_install.sh';     
            $mkdirCmd  = 'if [ -d ' . $installPath . ' ]; then rm -rf  ' . $installPath . '; fi;';
            $mkdirCmd .= 'if [ ! -d ' . $installPath . ' ]; then mkdir ' . $installPath . '; fi;';
            $screenCmd  = 'screen -dmS mumble ' . $scriptPath .'';
             
            $installScript = $twig->render(
                'DPMumbleServerBundle:sh:install_mumble.sh.twig', array('installDir' => $installPath));

            $screenCmd  = 'screen -dmS mumble ' . $scriptPath .'';

            // on éxecute les commandes shell
            $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
            $sec->exec($mkdirCmd);
            $sec->upload($scriptPath, $installScript);
            $sec->exec($screenCmd); 
            
            $this->uploadShellScript($twig, $screenName, $installPath);
                    
            // on vérifie que tout soit bien sur le serveur
            if ( $this->verificationServer() == 2)
                return 2;
            
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
            $installPath = $this->getAbsoluteDir();
            $rmCmd = $installPath . 'install.log';
            $shelldExist =  'if [ -f ' . $installPath . ' mumble.sh]; then echo "Shell upload" >> ' . $rmCmd . '; fi;';
            
            // on verifie que le log est présent et que l'install est bien fini :D
            $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
            $sec->exec($shelldExist);
            $installLog = $sec->exec('cat '. $rmCmd);
            
            if (strpos($installLog, 'Install ended') !== false && strpos($installLog, 'Shell upload') !== false){                
                return 2;
            }
            elseif(strpos($installLog, 'Install aborded')  !== false) {
                return 0;
            }
            else {
                return 1;
            } 
        }
    /*
     * Delete the path and all of the mumble server files
     */
    public function removeServer()
        {
            $installDir = $this->getDir();

            $rmCmd = 'if [ -d ' . $installDir . ' ]; then rm -f ' . $installDir . '; fi';

            $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
            $sec->exec($rmCmd);

            return 0;

        }
    /*
     *  Uplod Shell script to start mumble or stop it
     */    
    public function uploadShellScript(\Twig_Environment $twig, $screenName, $installPath)
        {        
          
           $status =  $mumbleScript = $twig->render(
                'DPMumbleServerBundle:sh:mumble.sh.twig', array(
                'screenName' => $screenName,
                'binDir' => $installPath
            ));

            $sec = PHPSeclibWrapper::getFromMachineEntity($this->getMachine());
            $sec->upload($mumbleScript, $installPath);
            var_dump($status); exit();
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