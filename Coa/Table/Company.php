<?php
namespace Coa\Table;

use Tk\Form\Field;
use Tk\Table\Cell;

/**
 * Example:
 * <code>
 *   $table = new Company::create();
 *   $table->init();
 *   $list = ObjectMap::getObjectListing();
 *   $table->setList($list);
 *   $tableTemplate = $table->show();
 *   $template->appendTemplate($tableTemplate);
 * </code>
 * 
 * @author Mick Mifsud
 * @created 2018-11-28
 * @link http://tropotek.com.au/
 * @license Copyright 2018 Tropotek
 */
class Company extends \Uni\TableIface
{

    /**
     * @return $this
     * @throws \Exception
     */
    public function init()
    {
    
        $this->appendCell(new Cell\Checkbox('id'));
        $this->appendCell($this->getActionCell());
        $this->appendCell(new Cell\Text('name'))->addCss('key');
        $this->appendCell(new Cell\Text('status'));
        $this->appendCell(new Cell\Text('email'));
        $this->appendCell(new Cell\Text('city'));
        $this->appendCell(new Cell\Text('country'));
//        $this->appendCell(new Cell\Text('units'))->setOrderProperty('SUM(p.units)');
//        $this->appendCell(new Cell\Text('placements'))->setOrderProperty('COUNT(p.id)');
//        $this->appendCell(new Cell\Text('cpd'))->setOrderProperty('SUM(p.units)');

        //$this->getFilterForm()->initForm();

        // Filters
        $this->appendFilter(new Field\Input('keywords'))->setAttr('placeholder', 'Search');
        $this->appendFilter(new Field\DateRange('date'));
        $subject = $this->getConfig()->getSubject();
        $value = array(
            'dateStart' => $subject->dateStart->format(\Tk\Date::FORMAT_SHORT_DATE),
            'dateEnd' => $subject->dateEnd->format(\Tk\Date::FORMAT_SHORT_DATE)
        );
        $this->getFilterForm()->load($value);

        // Actions
        /** @var \Coa\Db\Coa $coa */
        $coa = \Coa\Db\CoaMap::create()->find($this->getConfig()->getRequest()->get('coaId'));
        if ($coa)
            $this->appendAction(\Coa\Table\Action\Send::create($coa));

        $this->appendAction(\Tk\Table\Action\Csv::create());

        return $this;
    }

    /**
     * @param array $filter
     * @param null|\Tk\Db\Tool $tool
     * @return \Tk\Db\Map\ArrayObject|\App\Db\Company[]
     * @throws \Exception
     */
    public function findList($filter = array(), $tool = null)
    {
        if (!$tool) $tool = $this->getTool();
        $filter = array_merge($this->getFilterValues(), $filter);
        $list = \App\Db\CompanyMap::create()->findFiltered($filter, $tool);
        return $list;
    }

}