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

        $this->table = \Coa\Table\Company::create();
        $this->table->init();
        //$this->table->resetSessionOffset()->resetSessionTool();


        $filter = array(
            'profileId' => $this->getConfig()->getProfileId(),
            'hasEmail' => true
        );
        $this->table->setList($this->table->findList($filter));

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
    <p><small>*Note: Table only shows companies with a valid email</small></p>
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}