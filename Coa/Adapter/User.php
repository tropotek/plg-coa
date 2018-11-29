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
     * @param null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface|\App\Db\User $model
     * @return User|Iface
     */
    protected function setModel($model)
    {
        return parent::setModel($model);
    }
    
}