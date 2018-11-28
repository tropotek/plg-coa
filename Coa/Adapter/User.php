<?php
namespace Coa\Adapter;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class User extends Iface
{


    /**
     * @return null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface|\App\Db\User
     */
    public function getUser()
    {
        return $this->getModel();
    }

    
}