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

        $this->table = \App\Table\Company::createDynamicTable($this->getConfig()->getUrlName(), 'App\Db\Company', $this->getConfig()->getProfileId());
        $this->table->init();
        $this->table->removeCell('status');
        $this->table->removeCell('categoryClass');
        $this->table->removeCell('phone');
        $this->table->removeCell('private');
        $this->table->removeCell('modified');
        $this->table->removeCell('created');
        $this->table->appendCell(new \Tk\Table\Cell\Text('placements'))->setOrderProperty('COUNT(p.id)');
        $this->table->appendCell(new \Tk\Table\Cell\Text('units'))->setOrderProperty('SUM(p.units)');
        $this->table->removeFilter('status');
        $this->table->removeFilter('categoryClass');
        $this->table->removeFilter('categoryId');

        $this->table->getFilterForm()->initForm();
        $this->table->removeFilter('accom');

        $list = \App\Db\PlacementTypeMap::create()->findFiltered(array('profileId' => $this->getConfig()->getProfileId()));
        $this->table->appendFilter(new \Tk\Form\Field\CheckboxSelect('placementTypeId', $list));

        $this->table->appendFilter(new  \Tk\Form\Field\Input('minPlacements'))->setAttr('placeholder', 'Min. Placements');
        $this->table->appendFilter(new  \Tk\Form\Field\Input('minUnits'))->setAttr('placeholder', 'Min. Units');

        $this->table->appendFilter(new \Tk\Form\Field\DateRange('date'));
        $subject = $this->getConfig()->getSubject();
        $value = array(
            'dateStart' => $subject->dateStart->format(\Tk\Date::FORMAT_SHORT_DATE),
            'dateEnd' => $subject->dateEnd->format(\Tk\Date::FORMAT_SHORT_DATE)
        );
        $this->table->getFilterForm()->load($value);

        if ($this->coa)
            $this->table->prependAction(\Coa\Table\Action\Send::create($this->coa));

        $filter = array(
            'status' => \App\Db\Company::STATUS_APPROVED,
            'profileId' => $this->getConfig()->getProfileId(),
            'hasEmail' => true
        );

        $this->table->setList($this->findList($filter));
        $this->table->removeCell('accom');

    }

    /**
     * @param array $filter
     * @param null|\Tk\Db\Tool $tool
     * @return \Tk\Db\Map\ArrayObject|\App\Db\Company[]
     * @throws \Exception
     */
    public function findList($filter = array(), $tool = null)
    {
        if (!$tool) $tool = $this->table->getTool('FIELD(a.status, \'pending\', \'approved\') DESC');
        $filter = array_merge($this->table->getFilterValues(), $filter);
        $list = \App\Db\CompanyMap::create()->findWithPlacements($filter, $tool);
        return $list;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {

        //$this->getActionPanel()->add(\Tk\Ui\Button::create('Send', \Tk\Uri::create()->set('send'), 'fa fa-send'))->setAttr('title', 'Send to selected');

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