<?php
namespace Coa\Controller;



/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Student extends \Uni\Controller\AdminManagerIface
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
        $this->setPageTitle('Send To Students');
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

        $this->table = \Uni\Table\User::create();
        $this->table->init();

        $this->table->removeAction('delete');
        $this->table->removeCell('actions');
        $this->table->removeCell('username');
        $this->table->removeCell('phone');
        $this->table->removeCell('role');
        $this->table->removeCell('active');
        $this->table->removeCell('created');

        if ($this->coa)
            $this->table->prependAction(\Coa\Table\Action\Send::create($this->coa));


        $filter = array(
            'subjectId' => $this->getConfig()->getSubjectId(),
            'type' => \Uni\Db\Role::TYPE_STUDENT,
            'active' => true
        );
        $this->table->setList($this->table->findList($filter));

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
  <div class="tk-panel" data-panel-icon="fa fa-certificate" var="table"></div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}