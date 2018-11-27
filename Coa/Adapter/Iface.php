<?php
namespace Coa\Adapter;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
abstract class Iface
{
    /**
     * @var \Coa\Db\Coa
     */
    protected $coa = null;

    /**
     * @var \Tk\Db\Map\Model|\Tk\Db\ModelInterface
     */
    protected $model = null;

    /**
     * Iface constructor.
     * @param \Coa\Db\Coa $coa
     * @param \Tk\Db\Map\Model|\Tk\Db\ModelInterface $model
     * @throws \Tk\Exception
     */
    public function __construct($coa, $model)
    {
        $this->setCoa($coa);
        $this->setModel($model);
        if (!$this->model) {
            throw new \Tk\Exception('Invalid model for this adapter');
        }
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
    abstract public function getTemplateVars();


    /**
     * @return \Tk\Db\Map\Model|\Tk\Db\ModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface $model
     * @return Iface
     */
    protected function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return \Coa\Db\Coa
     */
    public function getCoa()
    {
        return $this->coa;
    }

    /**
     * @param \Coa\Db\Coa|null $coa
     * @return Iface
     */
    protected function setCoa($coa)
    {
        $this->coa = $coa;
        return $this;
    }

}