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
     * @var \Coa\Db\Coa
     */
    protected $coa = null;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('COA Edit');
    }

    /**
     * @param \Tk\Request $request
     * @return mixed
     * @throws \Exception
     */
    public function doDefault(\Tk\Request $request)
    {
        $this->coa = new \Coa\Db\Coa();
        $this->coa->profileId = $this->getConfig()->getProfileId();
        if ($request->get('coaId')) {
            $this->coa = \Coa\Db\CoaMap::create()->find($request->get('coaId'));
        }

        if ($request->has('preview')) {
            return $this->doPreview($request);
        }

        $this->setForm(\Coa\Form\Coa::create()->setModel($this->coa));
        $this->getForm()->execute();
    }

    /**
     * @param \Tk\Request $request
     * @return mixed
     * @throws \Exception
     */
    public function doPreview(\Tk\Request $request)
    {
        /** @var \Coa\Adapter\Iface $adapter */
        $adapter = null;
        switch ($this->coa->type) {
            case 'company':
                $company = \App\Db\CompanyMap::create()->findFiltered(array('profileId' => $this->coa->profileId), \Tk\Db\Tool::create('RAND()'))->current();
                $adapter = new \Coa\Adapter\Company($this->coa, $company);
                break;
            case 'staff':
                $staff = \App\Db\UserMap::create()->findFiltered(array('profileId' => $this->coa->profileId, 'type' => \Uni\Db\Role::TYPE_STAFF), \Tk\Db\Tool::create('RAND()'))->current();
                $adapter = new \Coa\Adapter\User($this->coa, $staff);
                break;
            case 'student':
                $student = \App\Db\UserMap::create()->findFiltered(array('subjectId' => $this->getConfig()->getSubjectId(), 'type' => \Uni\Db\Role::TYPE_STUDENT), \Tk\Db\Tool::create('RAND()'))->current();
                $adapter = new \Coa\Adapter\User($this->coa, $student);
                break;
        }

        $ren =  \Coa\Ui\PdfCertificate::create($adapter, 'Sample');
        //$ren->output();     // comment this to see html version
        return $ren->show();
    }

    /**
     * @return \Dom\Template
     * @throws \Exception
     */
    public function show()
    {
        if ($this->coa->getId()) {
            $this->getActionPanel()->add(\Tk\Ui\Button::create('Preview', \Uni\Uri::create()->set('preview'), 'fa fa-eye'))->setAttr('target', '_blank');
            $this->getActionPanel()->add(\Tk\Ui\Button::create('Send To',
                \Uni\Uri::createSubjectUrl($this->getRecipientSelectUrl($this->coa->type))
                    ->set('coaId', $this->coa->getId()), 'fa fa-envelope-o'));
        }
        $template = parent::show();
        
        // Render the form
        $template->appendTemplate('form', $this->form->show());

        return $template;
    }

    /**
     * @param $type
     * @return string
     */
    private function getRecipientSelectUrl($type)
    {
        $url = '/coaCompany.html';
        switch ($type) {
            case 'staff':
                $url = '/coaStaff.html';
                break;
            case 'student':
                $url = '/coaStudent.html';
                break;
        }
        return $url;
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