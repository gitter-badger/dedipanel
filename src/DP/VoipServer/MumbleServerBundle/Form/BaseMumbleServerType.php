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

namespace DP\VoipServer\MumbleServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BaseMumbleServerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('machine', 'entity', array(
                    'label' => 'mumble.selectMachine', 
                    'class' => 'DPMachineBundle:Machine'))
            ->add('portIce', 'integer', array( 
                    'label' => 'mumble.portIce'))
            ->add('iceSecret', 'text', array( 
                    'label' => 'mumble.iceSecret'))   
            ->add('dir', 'text', array( 
                    'label' => 'mumble.dir'))
            ->add('serverName', 'text', array( 
                    'label' => 'mumble.serverName'))
            ->add('serverPassword', 'text', array( 
                    'label' => 'mumble.serverPassword'))
            ->add('portMumble', 'integer', array( 
                    'label' => 'mumble.portMumble'))
            ->add('maxUser', 'integer', array( 
                    'label' => 'mumble.maxUser'))
            ->add('logName', 'text', array( 
                    'label' => 'mumble.logName'))
            ->add('logDuration', 'integer', array( 
                    'label' => 'mumble.logDuration'))
            ->add('welcomeText', 'text', array( 
                    'label' => 'mumble.welcomeText'))
            ->add('bandWidth', 'integer', array( 
                    'label' => 'mumble.bandWidth'))
        ;
    }

    public function getName()
    {
        return 'dp_voipserver_mumbleserverbundle_mumbleservertype';
    }
}
