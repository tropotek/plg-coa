<?php
namespace Coa\Adapter;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class Company extends Iface
{


    /**
     * @param null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface|\App\Db\Company $model
     * @return Company|Iface
     */
    protected function setModel($model)
    {
        return parent::setModel($model);
    }

}