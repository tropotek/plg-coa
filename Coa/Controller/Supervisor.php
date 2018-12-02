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

        $this->table = \App\Table\Supervisor::createDynamicTable($this->getConfig()->getUrlName(), 'App\Db\Supervisor', $this->getConfig()->getProfileId());
        $this->table->init();

//        $this->table->resetSessionTool();
//        $this->table->resetSessionOffset();

        $this->table->removeAction('delete');
        $this->table->removeCell('accom');
        $this->table->removeCell('modified');
        $this->table->removeCell('created');
        $this->table->appendCell(new \Tk\Table\Cell\Text('units'))->setOrderProperty('SUM(p.units)');
        $this->table->appendCell(new \Tk\Table\Cell\Text('placements'))->setOrderProperty('COUNT(p.id)');
        $this->table->appendCell(new \Tk\Table\Cell\Text('cpd'))->setOrderProperty('SUM(p.units)');

        $this->table->appendFilter(new  \Tk\Form\Field\Input('minUnits'))->setAttr('placeholder', 'Min. Units');
        $this->table->appendFilter(new  \Tk\Form\Field\Input('minPlacements'))->setAttr('placeholder', 'Min. Placements');
        $this->table->appendFilter(new  \Tk\Form\Field\Input('minCpd'))->setAttr('placeholder', 'Min. Cpd');


        if ($this->coa)
            $this->table->prependAction(\Coa\Table\Action\Send::create($this->coa));

        $filter = array(
            'profileId' => $this->getConfig()->getProfileId(),
            'hasEmail' => true
        );

        $tool = $this->table->getTool('created DESC');
        $filter = array_merge($this->table->getFilterValues(), $filter);
        $list = \App\Db\SupervisorMap::create()->findCpdTotals($filter, $tool);
        $this->table->setList($list);


        //$this->table->setList($this->table->findList($filter));

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
    <p><small>*Note: Table only shows supervisors with a valid email</small></p>
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}