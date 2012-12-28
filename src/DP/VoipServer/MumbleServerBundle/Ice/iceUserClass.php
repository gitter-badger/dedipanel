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

class User {

    private $container = array();
    private $containerUser = array();
    private $filter;

    /**
     * 
     * @return type
     */
    public function __construct($filter = Null) {

        /**
         * Recuperation de tout les utilisateurs 
         * sauf si $filter is not null
         */
        $this->user = $this->getRegisteredUsers($filter);

        if (is_null($filter)) {

            $this->users = count($this->user);
            if ($this->users > 0) {
                for ($i = 0; $i < $this->users; $i++) {
                    $this->getUser($this->user[$i]);
                    $this->container[$this->user[$i]] = $this->containerUser;
                }
            }
            return $this->container;
        }
        else
            return $this->container[$this->getUser($this->user)];
    }

    /**
     * 
     * @param type $user
     * @return type
     */
    public function getUser($user) {
        $this->user = $user;

        $this->containerUser = array(
            'session' => $this->getSession(),
            'adress' => $this->getUserID(),
            'name' => $this->getName(),
            'mute' => $this->isMute(),
            'deaf' => $this->isDeaf(),
            'supress' => $this->isSuppress(),
            'selfDeaf' => $this->isSelfMute(),
            'serlfMute' => $this->isSelfDeaf(),
            'registaration' => $this->getRegistration(),
            'clientVersion' => $this->getClientVersion(),
            'clientRelease' => $this->getClientRelease(),
            'comment' => $this->getComment(),
        );

        return $this->container;
    }

    /**
     * 
     * @return type
     */
    public function getSession() {
        return $this->user->session;
    }

    /**
     * 
     * @return type
     */
    public function getUserID() {
        return $this->user->userid;
    }

    /**
     * 
     * @return type
     */
    public function getName() {
        return $this->user->name;
    }

    /**
     * 
     * @return type
     */
    public function isMute() {
        return $this->user->mute;
    }

    /**
     * 
     * @return type
     */
    public function isDeaf() {
        return $this->user->deaf;
    }

    /**
     * 
     * @return type
     */
    public function isSuppress() {
        return $this->user->suppress;
    }

    /**
     * 
     * @return type
     */
    public function isSelfMute() {
        return $this->user->selfMute;
    }

    /**
     * 
     * @return type
     */
    public function isSelfDeaf() {
        return $this->user->selfDeaf;
    }

    /**
     * 
     * @return type
     */
    public function getClientVersion() {
        return $this->user->version;
    }

    /**
     * 
     * @return type
     */
    public function getClientRelease() {
        return $this->user->release;
    }

    /**
     * 
     * @return type
     */
    public function getComment() {
        return $this->user->comment;
    }

    /**
     * 
     * @param type $filter
     * @return type
     */
    public function getRegisteredUsers($filter = Null) {
        return $this->user->getRegisteredUsers($filter);
    }

    /**
     * 
     * @return type
     */
    public function getRegistration() {
        $this->id = $this->server->registerUser(array('NEWUSER'));
        return $this->server->updateRegistration($this->id, array('DP Membre' . $this->id));
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function unregisterUser($id) {
        return $this->server->unregisterUser($id);
    }

}