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

namespace DP\VoipServer\MumbleServerBundle\Ice\virtualServer;

class virtualServer extends Meta {
  
    
  private $_server
    
    /**
     * 
     */
    public function __construct($id) {  	
      $this->_server = parent::getServer($id);
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