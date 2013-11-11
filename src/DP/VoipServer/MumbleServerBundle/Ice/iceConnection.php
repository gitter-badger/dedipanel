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
 
 namespace DP\VoipServer\MumbleServerBundle\iceInitClass;

class iceConnection {

    private $ip;
    private $port;
    private $type;
    private $icesecret;
    private $connected;

    /**
     * 
     * @param type $ip
     * @param type $port
     * @param type $icesecret
	   * @global Ice_InitializationData $ICE
     */
    public function __construct($ip, $port, $icesecret = Null) {
      $this->ip = $ip;
      $this->port = $port;
      $this->context = $icesecret;
    }
    
    
    public function getConnection(){
    
      if (!extension_loaded('ice')) {
        echo 'IcePHP ne semble pas être installé sur votre serveur';
      }
      else {
          set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/share/php5/');
          require_once 'Ice.php';
          require_once 'Murmur.php';
      
          $endpoint = 'Meta:tcp -h ' . ICE_IP . ' -p ' . ICE_PORT . ' -t 2000';
      
          // Ice < 3.4
          if (!function_exists('Ice_intVersion') || Ice_intVersion() < 30400) {
              Ice_loadProfile();
              global $ICE;
      
              $base = $ICE->stringToProxy($endpoint);
      
              try {
                  $meta = $base->ice_checkedCast('::Murmur::Meta')->ice_context(ICE_SECRET);
                  
                  echo 'Connecté (< v3.4)';
              } catch (Ice_ConnectionRefusedException $exc) {
                  echo 'Non connecté (< v3.4)';
              }
          }
          // Ice >= 3.4
          else {
              $base = new Ice_InitializationData;
              $base->properties = Ice_createProperties();
              $base->properties->setProperty('Ice.ImplicitContext', ICE_SECRET);
      
              try {
                  $communicator = Ice_initialize($base);
                  $meta = Murmur_MetaPrxHelper::checkedCast($communicator->stringToProxy($endpoint));
                  
                  echo 'Connecté (>= 3.4)';
              } catch (Ice_ConnectionRefusedException $exc) {
                  echo 'Non connecté (>= 3.4)';
              }
          }
      }
    }

    /**
     * 
     * @param Ice_InitializationData $ICE
     * @throws NotConnectedException
     */
    public function disconnect($ICE) {
        if (!$this->connected) {
            throw new NotConnectedException('Connection is already disconnected.');
        }

        $this->connected = false;
        try {
            $ICE->destroy();
        } catch (Ice_LocalException $ex) {
            trigger_error($this->getLastError(), E_USER_WARNING);
        }
    }

    /**
     * Get the last error message // nirrrrr hellllppppp :'(
     * @return string 
     */
    private function getLastError() {
        return;
    }
}