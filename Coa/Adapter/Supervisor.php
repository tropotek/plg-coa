<?php
namespace Coa\Adapter;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class Supervisor extends Iface
{


    /**
     * @param null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface|\App\Db\Supervisor $model
     * @return Iface|Supervisor
     * @throws \Exception
     */
    protected function setModel($model)
    {
        parent::setModel($model);

//        $totals = current(\App\Db\SupervisorMap::create()->findFilteredWithTotals(array('id' => $model->id)));
//        if ($totals) {
//            $this->replace((array)$totals);
//        }

        return $this;
    }

    /**
     * Calculate the Cpd based on the total number of placement units
     *
     *
     * @param $totalUnits
     * @return int
     */
    static function calculateCpd($totalUnits)
    {
        $cpd = ($totalUnits * 5);
        return ($cpd > 20) ? 20 : $cpd;
    }
}