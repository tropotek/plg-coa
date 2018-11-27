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
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Sent To Company');
    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function doDefault(\Tk\Request $request)
    {

        $this->table = \App\Table\Coa::create()->init();

        $this->table->setList($this->table->findList(array('profileId' => $this->getConfig()->getProfileId())));

    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $this->getActionPanel()->add(\Tk\Ui\Button::create('Add Coa', \Uni\Uri::createSubjectUrl('/coaEdit.html'), 'fa fa-certificate'));
        $template = parent::show();

        $template->appendTemplate('table', $this->table->show());
        
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
  <div class="tk-panel" data-panel-icon="fa fa-certificate" var="table"></div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}