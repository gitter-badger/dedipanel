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

class Ice {

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
     */
    public function __construct($ip, $port, $icesecret = Null) {

        $this->ip = $ip;
        $this->port = $port;
        $this->icesecret = $icesecret;
        $this->connected = false;
    }

    /**
     * 
     * @global Ice_InitializationData $ICE
     */
    public function connect() {
        if (!extension_loaded('ice')) {
            trigger_error('IcePHP ne semble pas être installé sur votre serveur', E_USER_ERROR);
        } else {


            $this->endpoint = 'Meta:tcp -h ' . $this->ip . ' -p ' . $this->port . ' -t 2000';

            /**
             * @ Ice =< v3.3
             */
            if (!function_exists('Ice_intVersion') || Ice_intVersion() < 30400) {

                Ice_loadProfile();
                global $ICE;

                $base = $ICE->stringToProxy($this->endpoint);

                try {
                    $this->meta = $base->ice_checkedCast('::Murmur::Meta')->ice_context($this->icesecret);
                    $this->connected = true;
                } catch (Ice_ConnectionRefusedException $exc) {
                    trigger_error($this->getLastError(), E_USER_WARNING);
                }
            }
            /**
             * @ Ice >= v3.4
             */ else {

                $ICE = new Ice_InitializationData;
                $ICE->properties = Ice_createProperties();
                $ICE->properties->setProperty('Ice.ImplicitContext', $this->icesecret);

                try {
                    $this->meta = Murmur_MetaPrxHelper::checkedCast($base->stringToProxy($this->endpoint));
                    $this->connected = true;
                } catch (Ice_ConnectionRefusedException $exc) {
                    trigger_error($this->getLastError(), E_USER_WARNING);
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

    /**
     * Set IP
     * 
     * @param string $ip 
     */
    public function setIp($ip) {
        $this->ip = $ip;
    }

    /**
     * Get IP
     * 
     * @return string
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * Set port
     * 
     * @param int $port 
     */
    public function setPort($port) {
        $this->port = $port;
    }

    /**
     * Get port
     * 
     * @return int 
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Set icesecret
     * 
     * @param int $type 
     */
    public function setIcesecret($icesecret) {
        $this->type = $icesecret;
    }

    /**
     * Get icesecret
     * 
     * @return int 
     */
    public function getIcesecret() {
        return $this->type;
    }

    /**
     * Get socket status (connected or not)
     * 
     * @return bool
     */
    public function isConnected() {
        return $this->connected;
    }

}