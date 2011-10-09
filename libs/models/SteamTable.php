<?php

/**
 * SteamTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SteamTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object SteamTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Steam');
    }

    // TODO: Verif sql ($vm && $port) || ($vm && $dir)
    public function exists($vm, $port, $dir) {
        $q = Doctrine_Query::create()
            ->from('Steam')
            ->where('idVm = ?', $vm)
            ->andWhere('port = ?', $port)
            ->fetchOne();

        return ($q == false) ? false : true;
    }
}