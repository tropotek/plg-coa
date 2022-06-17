<?php
namespace Coa\Controller;



/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Company extends \Uni\Controller\AdminManagerIface
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
        $this->setPageTitle('Send To Company');
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

        $this->setTable(\App\Table\Company::createDynamicTable($this->getConfig()->getUrlName(), 'App\Db\Company'));
        $this->getTable()->init();
        $this->getTable()->removeCell('status');
        $this->getTable()->removeCell('categoryClass');
        $this->getTable()->removeCell('phone');
        $this->getTable()->removeCell('private');
        $this->getTable()->removeCell('modified');
        $this->getTable()->removeCell('created');
        $this->getTable()->appendCell(new \Tk\Table\Cell\Text('placements'))->setOrderProperty('COUNT(p.id)');
        $this->getTable()->appendCell(new \Tk\Table\Cell\Text('units'))->setOrderProperty('SUM(p.units)');
        $this->getTable()->removeFilter('status');
        $this->getTable()->removeFilter('categoryClass');
        $this->getTable()->removeFilter('categoryId');
        $this->getTable()->getFilterForm()->initForm();
        $this->getTable()->removeFilter('accom');

        $list = \App\Db\PlacementTypeMap::create()->findFiltered(array('courseId' => $this->getConfig()->getCourseId(), 'active' => true));
        $this->getTable()->appendFilter(new \Tk\Form\Field\CheckboxSelect('placementTypeId', $list));

        $this->getTable()->appendFilter(new  \Tk\Form\Field\Input('minPlacements'))->setAttr('placeholder', 'Min. Placements');
        $this->getTable()->appendFilter(new  \Tk\Form\Field\Input('minUnits'))->setAttr('placeholder', 'Min. Units');

        $this->getTable()->appendFilter(new \Tk\Form\Field\DateRange('date'));
        $subject = $this->getConfig()->getSubject();
        $value = array(
            'dateStart' => $subject->dateStart->format(\Tk\Date::FORMAT_SHORT_DATE),
            'dateEnd' => $subject->dateEnd->format(\Tk\Date::FORMAT_SHORT_DATE)
        );
        $this->getTable()->getFilterForm()->load($value);

        if ($this->coa)
            $this->getTable()->prependAction(\Coa\Table\Action\Send::create($this->coa));

        $filter = array(
            'status' => \App\Db\Company::STATUS_APPROVED,
            'courseId' => $this->getConfig()->getCourseId(),
            'hasEmail' => true
        );

        $this->getTable()->setList($this->findList($filter));
        $this->getTable()->removeCell('accom');

    }

    /**
     * @param array $filter
     * @param null|\Tk\Db\Tool $tool
     * @return \Tk\Db\Map\ArrayObject|\App\Db\Company[]
     * @throws \Exception
     */
    public function findList($filter = array(), $tool = null)
    {
        if (!$tool) $tool = $this->getTable()->getTool('FIELD(a.status, \'pending\', \'approved\') DESC');
        $filter = array_merge($this->getTable()->getFilterValues(), $filter);
        $list = \App\Db\CompanyMap::create()->findWithPlacements($filter, $tool);
        return $list;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        $template->prependTemplate('table', $this->table->show());
        
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
<div>
  <div class="tk-panel" data-panel-icon="fa fa-certificate" var="table">
    <p>&nbsp;</p>
    <p>
      <small>* Results only show companies with a valid email, that are in the Approved status and that have had a placement within the supplied dates.</small>
    </p>
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}