<?php
namespace Coa\Controller;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Edit extends \Uni\Controller\AdminEditIface
{

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('COA Edit');
    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function doDefault(\Tk\Request $request)
    {
        $coa = new \Coa\Db\Coa();

        if ($request->get('coaId')) {
            $coa = \Coa\Db\CoaMap::create()->find($request->get('coaId'));
        }

        $this->setForm(\Coa\Form\Coa::create()->setModel($coa));
        $this->getForm()->execute();
    }

    /**
     * @return \Dom\Template
     * @throws \Exception
     */
    public function show()
    {
        $template = parent::show();
        
        // Render the form
        $template->appendTemplate('form', $this->form->show());

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
  <div class="tk-panel" data-panel-icon="fa fa-certificate" var="form"></div>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}