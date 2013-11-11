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

namespace DP\VoipServer\MumbleServerBundle\Ice\Meta;

use DP\VoipServer\MumbleServerBundle\Ice\VirtualServer\virtualServer;
use DP\VoipServer\MumbleServerBundle\Ice\VirtualServer\virtualUser;
use DP\VoipServer\MumbleServerBundle\Ice\VirtualServer\virtualAction;
use DP\VoipServer\MumbleServerBundle\Ice\iceConnection;

class Meta { 

  private $meta
  
  public function __construct(){
    $this->meta = iceConnection::getConnection($ip, $port, $icesecret = Null);
    
  }
  /**
   * 
   */
  public function setServer(){
  
    $this->meta->newServer();
  }
  /**
   * @param $id 
   * return
   */
  public function getServer($id){
    return $this->meta->getServer($id);
    
  }
  /**
   * @return
   */ 
  public function getBootedServers(){
  
    return $this->meta->getbootedServer();    
  } 
  /**
   * @return
   */  
  public function getMumbleConf(){
  
    return $this->meta->getdefaultConf();    
  }
  /**
   * @return
   */    
  public function GetMumbleVersion(){
  
    return $this->meta->getVersion();    
  }
}
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  