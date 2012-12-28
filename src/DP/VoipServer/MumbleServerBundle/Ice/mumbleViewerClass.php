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

class Viewer extends Server {
    
    

    public function viewer($sid) {
        $tree = $this->treeView($sid);
        $viewer = $this->channel($tree, $sid, 0, true, false);

        return $viewer;
    }

    public function treeView($sid) {
        $serv = $this->meta->getServer($sid)->ice_context($this->context);
        return $serv->getTree();
    }

    private function getTooltipChannel($sid, $chan) {
        $view = '<span class="tooltip" id="tooltip::' . $chan->id . '">
                    ' . $chan->name . '
                </span>
                <div id="tooltip-content::' . $chan->id . '" class="tip-title" style="display: none;">
                    ID : ' . $chan->id . '<br />
                    <a href="#" onclick="channelAdd(' . $this->mid . ', ' . $sid . ', ' . $chan->id . ')">Ajouter un sous channel</a><br />
                    <a href="#" onclick="channelDel(' . $this->mid . ', ' . $sid . ', ' . $chan->id . ')">Supprimer ce channel</a><br />
                </div>';

        return $view;
    }

    public function channel($tree, $sid, $pos = 0, $start = false, $end = false) {
        $viewer = '';
        $children = count($tree->children);

        if ($start == true) {
            $viewer .= '
                <span class="mumble_line">
                    <img src="./images/mumble/mumble.png" />' . $this->getTooltipChannel($sid, $tree->c) . '
                </span><br />';
        }

        for ($i = 0; $i < $children; $i++) {
            $chan = $tree->children[$i]->c;
            $fin = $end;
            if (($i + 1 == $children && $start === true) || $fin)
                $fin = true;

            if ($chan->parent == 0) {

                if (!$end && $fin) {
                    $viewer .= '
                        <span class="mumble_line">
                            <img src="./images/mumble/branche-fin.png" /><img src="./images/mumble/channel.png" />
                            ' . $this->getTooltipChannel($sid, $chan) . '
                        </span><br />';
                } else {
                    $viewer .= '
                        <span class="mumble_line">
                            <img src="./images/mumble/branche-plus.png"><img src="./images/mumble/channel.png">
                            ' . $this->getTooltipChannel($sid, $chan) . '
                        </span><br />';
                }
            } else {
                if ($end) {
                    $viewer .= '
                        <span class="mumble_line">
                            <img src="./images/mumble/branche-vide.png" />';
                } else {
                    $viewer .= '
                        <span class="mumble_line">
                            <img src="./images/mumble/branche.png" />';
                }

                for ($j = 0; $j < $pos; $j++) {
                    $viewer .= '
                            <img src="./images/mumble/branche-vide.png" />';
                }

                $viewer .= '
                        <img src="./images/mumble/branche-fin.png" />
                        <img src="./images/mumble/channel.png" />
                        ' . $this->getTooltipChannel($sid, $chan) . '
                    </span><br />';
            }

            if (count($tree->children[$i]->children) > 0 || count($tree->children[$i]->users) > 0) {
                $viewer .= $this->channel($tree->children[$i], $sid, $pos + 1, false, $fin);
            }
        }

        $users = count($tree->users);

        if ($users > 0) {
            for ($i = 0; $i < $users; $i++) {
                $viewer .= '<span class="mumble_line"><img src="./images/mumble/branche.png">';

                for ($j = 0; $j < $pos; $j++) {
                    $viewer .= '<img src="./images/mumble/branche-vide.png">';
                }

                $viewer .= $this->user($tree->users[$i], $sid);
            }
        }
        return $viewer;
    }

}