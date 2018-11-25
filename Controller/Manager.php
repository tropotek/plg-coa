<?php
namespace Coa\Controller;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Manager extends \Uni\Controller\AdminManagerIface
{



    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('COA Manager');
    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function doDefault(\Tk\Request $request)
    {

        $editUrl = \Uni\Uri::createSubjectUrl('/coaEdit.html');
        $this->table = \Bs\Table\User::create()->setEditUrl($editUrl)->init();
        $this->table->setList($this->table->findList(array()));

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