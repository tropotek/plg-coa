<?php
namespace Coa\Controller;



/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Supervisor extends \Uni\Controller\AdminManagerIface
{

    /**
     * @var null|\Coa\Db\Coa
     */
    protected $coa = null;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Send To Supervisor');
    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function doDefault(\Tk\Request $request)
    {

        $this->coa = \Coa\Db\CoaMap::create()->find($request->get('coaId'));
        if (!$this->coa) {
            \Tk\Alert::addError('Cannot locate Coa object. Please contact your administrator.');
            $this->getConfig()->getBackUrl()->redirect();
        }

        $this->setTable(\App\Table\Supervisor::createDynamicTable($this->getConfig()->getUrlName(), 'App\Db\Supervisor',
            $this->getConfig()->getCourseId()));
        $this->getTable()->init();

        $this->getTable()->removeAction('delete');
        $this->getTable()->removeCell('accom');
        $this->getTable()->removeCell('status');
        $this->getTable()->removeCell('modified');
        $this->getTable()->removeCell('created');
        $this->getTable()->appendCell(new \Tk\Table\Cell\Text('placements'))->setOrderProperty('COUNT(p.id)');
        $this->getTable()->appendCell(new \Tk\Table\Cell\Text('units'))->setOrderProperty('SUM(p.units)');
        $this->getTable()->appendCell(new \Tk\Table\Cell\Text('cpd'))->setOrderProperty('SUM(p.units)');
        $this->getTable()->removeFilter('status');
        $this->getTable()->removeFilter('graduationYear');


        $this->getTable()->appendFilter(new \Tk\Form\Field\DateRange('date'));
        $subject = $this->getConfig()->getSubject();
        $value = array(
            'dateStart' => $subject->dateStart->format(\Tk\Date::FORMAT_SHORT_DATE),
            'dateEnd' => $subject->dateEnd->format(\Tk\Date::FORMAT_SHORT_DATE)
        );
        $this->getTable()->getFilterForm()->load($value);

        $list = \App\Db\PlacementTypeMap::create()->findFiltered(array('courseId' => $this->getConfig()->getCourseId()));
        $this->getTable()->appendFilter(new \Tk\Form\Field\CheckboxSelect('placementTypeId', $list));

        $this->getTable()->appendFilter(new  \Tk\Form\Field\Input('minPlacements'))->setAttr('placeholder', 'Min. Placements');
        $this->getTable()->appendFilter(new  \Tk\Form\Field\Input('minUnits'))->setAttr('placeholder', 'Min. Units');
        //$this->table->appendFilter(new  \Tk\Form\Field\Input('minCpd'))->setAttr('placeholder', 'Min. Cpd');

        if ($this->coa)
            $this->getTable()->prependAction(\Coa\Table\Action\Send::create($this->coa));

        $filter = array(
            'status' => \App\Db\Supervisor::STATUS_APPROVED,
            'courseId' => $this->getConfig()->getCourseId(),
            'hasEmail' => true
        );

        $tool = $this->getTable()->getTool('created DESC');
        $filter = array_merge($this->getTable()->getFilterValues(), $filter);
        $list = \App\Db\SupervisorMap::create()->findCpdTotals($filter, $tool);
        $this->getTable()->setList($list);

    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        $template->prependTemplate('panel', $this->getTable()->show());
        
        return $template;
    }

    /**
     * DomTemplate magic method
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="tk-panel" data-panel-icon="fa fa-certificate" var="panel">
  <p>&nbsp;</p>
  <p><small>*Note: Table only shows supervisors with a valid email</small></p>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}