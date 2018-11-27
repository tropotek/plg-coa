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
     * @param null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface $model
     * @return Iface
     */
    protected function setModel($model)
    {
        if ($model instanceof \App\Db\Company)
            $this->model = $model;
        return $this;
    }

    /**
     * @return null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface|\App\Db\Company
     */
    public function getCompany()
    {
        return $this->getModel();
    }


    /**
     * return an array that will replace the template curly brace parameters
     * EG
     *   {name}, {cpd}, {dateFrom}
     *
     * return:
     *   array('name' => 'Some Name', 'cpd' => 15, 'dateFrom' => '15 Mar 2019');
     *
     * @return array|string[]
     */
    public function getTemplateVars()
    {
        return array();
    }
}