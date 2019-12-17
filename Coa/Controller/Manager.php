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

        $this->setTable(\Coa\Table\Coa::create()->init());

        $this->getTable()->setList($this->getTable()->findList(array('courseId' => $this->getConfig()->getCourseId())));

    }

    /**
     *
     */
    public function initActionPanel()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('Add Coa', \Uni\Uri::createSubjectUrl('/coaEdit.html'), 'fa fa-certificate'));
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $this->initActionPanel();
        $template = parent::show();

        $template->appendTemplate('panel', $this->getTable()->show());
        
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
<div class="tk-panel" data-panel-icon="fa fa-certificate" var="panel"></div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}