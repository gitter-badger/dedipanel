<?php

/*
** Copyright (C) 2010-2012 Kerouanton Albin, Smedts Jérôme
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along
** with this program; if not, write to the Free Software Foundation, Inc.,
** 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace DP\GameServer\SteamServerBundle\SteamQuery;

use DP\GameServer\GameServerBundle\Socket\Socket;
use DP\GameServer\GameServerBundle\Socket\Packet;
use DP\GameServer\GameServerBundle\Socket\PacketCollection;

use DP\GameServer\GameServerBundle\Socket\Exception\ConnectionFailedException;
use DP\GameServer\GameServerBundle\Socket\Exception\NotConnectedException;
use DP\GameServer\GameServerBundle\Socket\Exception\RecvTimeoutException;
use DP\GameServer\SteamServerBundle\SteamQuery\Exception\ServerTimeoutException;

class SourceRcon
{
    private $socket;
    private $packetFactory;
    private $rconPassword;
    private $authenticated = null;
    
    public function __construct($container, $host, $port, $rconPassword)
    {
        /*$callback = function(Packet $packet, Socket $socket) {
            if (is_null($packet) || $packet->isEmpty()) return false;
            
            $remaining = $packet->getLong(false);
            $packet->setPos(4);
            $id = $packet->getLong(false);
            
            if ($remaining > 0) {
                $splittedPackets = new PacketCollection();
                $respId = null;
                
                do {
                    if (!$respId) {
                        $respId = $id;
                    }
                    elseif ($respId != $id) {
                        $packet = $socket->recv(false);
                        continue;
                    }
                    
                    $splittedPackets->add($packet->rewind());
                    $packet = $socket->recv(false, $remaining);
                    $remaining -= $packet->getLength();
                } while ($remaining > 0);
                
                return $splittedPackets->reassemble(
                    function(Packet $bigPacket, Packet $packet) {
                        if ($bigPacket->isEmpty()) {
                            $bigPacket->addContent($packet->getContent());
                            $bigPacket->setPos($packet->getLength()-2);
                        }
                        else {
                            $bigPacket->addContent($packet);
                        }
                        
                        return $bigPacket;
                    }
                )->rewind();
            }
            else {
                return $packet;
            }
        };*/
        $callbacks = array();
        // Permet de déterminé s'il s'agit d'une réponse multi-paquet
        $callbacks[] = function(Packet $packet) {
            if (is_null($packet) || $packet->isEmpty()) return false;
            
            // Récupération de la longueur de la réponse
            $len = $packet->getLong(false);
            // Il faut rajouter 4 puisque la taille récupéré correspondant 
            // A la taille du paquet sans l'entier contenant la taille
            $remaining = $len - $packet->getLength() + 4;
            
            if ($remaining > 0) {                
                return true;
            }
            else {
                return false;
            }
        };
        // Permet de récupérer les différents paquets qui composent une réponse
        $callbacks[] = function(Packet $packet, Socket $socket) {
            if (is_null($packet) || $packet->isEmpty()) return false;
            
            // Récupération de la longueur de la réponse
            $len = $packet->getLong(false);
            // Il faut rajouter 4 puisque la taille récupéré correspondant 
            // A la taille du paquet sans l'entier contenant la taille
            $remaining = $len - $packet->getLength() + 4;
            
            while ($remaining > 0) {
                // Récupération du prochain paquet et calcul de la taille restante à récupérer
                $newPacket = $socket->recv(false, $remaining);
                $remaining = $remaining - $newPacket->getLength();

                // Ajout du paquet au paquet originel
                $packet->setPos($packet->getLength() - 2);
                $packet->addContent($newPacket->getContent());
            }
            
            return $packet;
        };
        
        $this->rconPassword = $rconPassword;
        $this->socket = $container->get('socket')->getTCPSocket($host, $port, $callbacks);
        $this->packetFactory = $container->get('packet.factory.rcon.source');
        
        try {
            $this->socket->connect();
            $this->auth();
        }
        catch (ConnectionFailedException $e) {}
    }
    
    private function auth()
    {
        if ($this->authenticated == null) {
            $id = null;
            $packet = $this->packetFactory->getAuthPacket($id, $this->rconPassword);            
            $this->socket->send($packet);
            $resp = $this->socket->recv(false);
            
            if ($resp->isEmpty()) {
                $this->authenticated = false;
                return;
            }
            
            $infos = $resp->extract(array(
                'size' => 'long', 
                'id' => 'long', 
                'type' => 'long', 
                's1' => 'string', 
                's2' => 'string'
            ));
            
            if ($infos['type'] == $this->packetFactory->SERVER_RESPONSE_VALUE) {
                $resp = $this->socket->recv(false);
                $infos = $resp->extract(array(
                    'size' => 'long', 
                    'id' => 'long', 
                    'type' => 'long', 
                    's1' => 'string', 
                    's2' => 'string'
                ));
            }
            
            if ($infos['id'] == $id && 
                $infos['type'] == $this->packetFactory->SERVER_AUTH_RESPONSE) {
                $this->authenticated = true;
            }
            else {
                $this->authenticated = false;
            }
        }
    }
    
    public function sendCmd($cmd)
    {
        if ($this->authenticated) {
            $id = null;
            $packet = $this->packetFactory->getCmdPacket($id, $cmd);
            $this->socket->send($packet);
            
            $resp = $this->recv();
            
            if ($resp == null) {
                return false;
            }
            
            $resp = $resp->rewind()->extract(array(
                'size' => 'long', 
                'id' => 'long', 
                'type' => 'long', 
                'body' => 'string', 
            ));
            
            if ($resp['id'] != $id || $resp['type'] != $this->packetFactory->SERVER_RESPONSE_VALUE) {
                return false;
            }
            
            return $resp['body'];
        }
        else {
            return false;
        }
    }
    
    /**
     * Get mulitple packets from socket recv method
     * Return reassembled packets if there is reponses
     * Or return null if there is a RecvTimeoutException catched and any response recovered
     * before any content has been received.
     * 
     * @return \DP\GameServer\GameServerBundle\Socket\Packet|null
     */
    private function recv()
    {
        $packets = new PacketCollection();
        
        do {
            try {
                $resp = $this->socket->recv();
                $packets->add($resp->rewind());
            }
            catch (RecvTimeoutException $e) {
                $resp = null;
            }
        } while ($resp != null);
        
        // Verif que tous les paquets ont bien été reçus
        // Si c'est le cas on renvoie les données reçus, sinon on renvoie rien
        if ($packets->count() > 0 && $this->verifyAllPacketsReceived()) {
            $packetFactory = $this->packetFactory;

            return $packets->reassemble(function (Packet $bigPacket, Packet $packet) use ($packetFactory) {
                if ($bigPacket->isEmpty()) {
                    $bigPacket->addContent($packet);
                }
                else {
                    $bigPacket->rewind();

                    // Ajout de la taille du packet au bigPacket
                    $packetSize = $packet->getLong(false);
                    $bigPacketSize = $bigPacket->getLong();
                    $newSize = $packetSize + $bigPacketSize;

                    $bigPacket->rewind()->addContent(
                        $packetFactory->transformLong($newSize)
                    );

                    // Ajout du contenu au bigPacket                
                    $bigPacket->setPos($bigPacket->getLength()-4)->addContent(
                        substr($packet->setPos(12)->getString(), 0, -4)
                    );
                }

                return $bigPacket;
            });
        }
        
        return null;
    }
    
    public function verifyAllPacketsReceived()
    {
        $id = null;
        $packet = $this->packetFactory->getEmptyResponsePacket($id);
        $this->socket->send($packet);
        
        if ($this->socket->recv(false) == $packet) {
            $resp = $this->socket->recv(false);
            $resp->setPos(13);
            
            if ($resp->getByte() == 1) {
                return true;
            }
        }
        
        return false;
        
    }
}
