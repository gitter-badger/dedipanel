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

class VirtualUser extends Meta{
    
    private $_server;
    
    /**
     * 
     * @return type
     */
    public function __construct($id) {  	
      $this->_server = parent::getServer($id);
    }
       
    /**
     * 
     * @param type $filter
     * @return type
     */
    public function getUsers($filter = Null) {
        return $this->_server->getRegisteredUsers($filter);
    }

    /**
     * 
     * @return type
     */
    public function setRegistration($user) {
        $this->id = $this->_server->registerUser(array('NEWUSER'));
        $this->_server->updateRegistration($this->id, array($user));
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function deleteUser($id) {
        $this->_server->unregisterUser($id);
    }

}