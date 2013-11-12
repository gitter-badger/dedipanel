<?php

/*
Copyright (C) 2010-2012 Kerouanton Albin, Smedts Jérôme

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace DP\VoipServer\MumbleServerBundle\Ice\VirtualServer;

class VirtualServer extends Meta {
      
  private $_server
    
    /**
     * 
     */
    public function __construct($id) {  	
      $this->_server = parent::getServer($id);
    }
    
    /**
    * 
    */
    public function isRunning() {
      return $this->_server->isRunning();      
    }	
    /**
    * 
    */
    public function setState($state) {
      
      switch ($state)
      { 
        case "start":
        $this->_server->start(); 
        break;
        
        case "stop":
        $this->_server->stop();
        break;
        
        case "restart":
        if($this->isRunning() == true)
           $this->_server->stop();
        
        $this->_server->start();         
        break;
        
        case "delete":
        if($this->isRunning() == true)
           $this->_server->stop();
        
        $this->_server->delete();
        break;    
        }
     }
	  /**
     * 
     * @return type
     */
    public function createServer() {
        $this->_server->newServer();
    }

    /**
     * 
     * @return type
     */
    public function getVersion() {
        return $this->_server->getVersion($this->major, $this->minor, $this->patch, $this->text);
    }

    /**
     * 
     * @return type
     */
    public function getRunningServers() {
        return $this->_server->getBootedServers();
    }

    /**
     * 
     * @return array with configuration
     */
    public function getAllConf() {
        return $this->_server->getAllConf();
    }

    /**
     * 
     * @param type $key
     * @return 
     */
    public function getConf($key) {
        return $this->_server->getConf($key);
    }

    /**
     * 
     * @param type $key
     * @param type $value
     */
    public function setConf($key, $value) {
        $this->_server->setConf($key, $value);
    }

    /**
     * 
     * @param type $newPw
     * @return type
     */
    public function setSuperuserPassword($password) {
        return $this->_server->setSuperuserPassword($password);
    }

    /**
     * 
     * @param type $startRowFromEnd
     * @param type $endRow
     * @return type
     */
    public function getLog() {
        return $this->_server->getLog(0, 240);
    }
        /**
     * 
     * @return type
     */
    function setBan() {

        if (!is_int($this->user->address)) {
            $ip_explode = explode(".", $this->user->address);
            $this->user->address = $ip_explode[0] * 256 * 256 * 256 + $ip_explode[1] * 256 * 256 + $ip_explode[2] * 256 + $ip_explode[3];
        }

        $ban = new Murmur_Ban();
        $ban->name = $this->user->name;
        $ban->address = $this->user->address;
        $ban->bits = $this->bits;
        $ban->reason = $this->raison;
        $ban->duration = 1;
        $ban->start = 1;

        $bannir[] = $ban;

        $this->server->setBans($bannir);
    }

    /**
     * 
     * @return type
     */
    function setUnBan($session) {

        new user($session);
        $banni = $this->server->getBans();

        unset($banni[$this->user->id]);
        $this->server->setBans($banni);
    }

    /**
     * 
     * @param type $sessionId
     * @param type $raison
     * @return type
     */
    public function kickUser($session, $raison = '') {
        return $this->server->kickUser($session, $raison);
    }

    /**
     * 
     * @return type
     */
    public function removeChannel() {
        return $this->server->removeChannel();
    }

    /**
     * 
     * @return type
     */
    public function setChannel() {
        return $this->server->addChannel();
    }
}