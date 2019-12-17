<?php
namespace Coa\Table;

use Tk\Form\Field;
use Tk\Table\Cell;

/**
 * Example:
 * <code>
 *   $table = new Coa::create();
 *   $table->init();
 *   $list = ObjectMap::getObjectListing();
 *   $table->setList($list);
 *   $tableTemplate = $table->show();
 *   $template->appendTemplate($tableTemplate);
 * </code>
 * 
 * @author Mick Mifsud
 * @created 2018-11-26
 * @link http://tropotek.com.au/
 * @license Copyright 2018 Tropotek
 */
class Coa extends \Uni\TableIface
{
    
    /**
     * @return $this
     * @throws \Exception
     */
    public function init()
    {
        $this->appendCell(new Cell\Checkbox('id'));
        //$this->appendCell(new Cell\Text('profileId'));
        $this->appendCell(new Cell\Text('msgSubject'))->addCss('key')->setUrl(\Uni\Uri::createSubjectUrl('/coaEdit.html'));
        $this->appendCell(new Cell\Text('type'));
        //$this->appendCell(new Cell\Date('modified'));
        $this->appendCell(new Cell\Date('created'));

        // Filters
        $this->appendFilter(new Field\Input('keywords'))->setAttr('placeholder', 'Search');

        // Actions
        //$this->appendAction(\Tk\Table\Action\Link::create('New Coa', 'fa fa-plus', \Uni\Uri::createHomeUrl('/coaEdit.html')));
        //$this->appendAction(\Tk\Table\Action\ColumnSelect::create()->setUnselected(array('modified', 'created')));
        $this->appendAction(\Tk\Table\Action\Delete::create());
        $this->appendAction(\Tk\Table\Action\Csv::create());

        // load table
        //$this->setList($this->findList());
        
        return $this;
    }

    /**
     * @param array $filter
     * @param null|\Tk\Db\Tool $tool
     * @return \Tk\Db\Map\ArrayObject|\Coa\Db\Coa[]
     * @throws \Exception
     */
    public function findList($filter = array(), $tool = null)
    {
        if (!$tool) $tool = $this->getTool();
        $filter = array_merge($this->getFilterValues(), $filter);
        $list = \Coa\Db\CoaMap::create()->findFiltered($filter, $tool);
        return $list;
    }

}