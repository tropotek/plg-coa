<?php
namespace Coa\Form;

use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form;

/**
 * Example:
 * <code>
 *   $form = new Coa::create();
 *   $form->setModel($obj);
 *   $formTemplate = $form->getRenderer()->show();
 *   $template->appendTemplate('form', $formTemplate);
 * </code>
 * 
 * @author Mick Mifsud
 * @created 2018-11-26
 * @link http://tropotek.com.au/
 * @license Copyright 2018 Tropotek
 */
class Coa extends \Bs\FormIface
{

    /**
     * @throws \Exception
     */
    public function init()
    {

        $list = array('Company' => 'company', 'Staff' => 'staff', 'Student' => 'student');
        $this->appendField(new Field\Select('type', $list))->prependOption('-- Select --', '');
        //$this->appendField(new Field\Input('type'));
        $this->appendField(new Field\Input('subject'));
        $this->appendField(new Field\Textarea('html'))
            ->addCss('mce')->setAttr('data-elfinder-path', $this->getCoa()->getProfile()->getInstitution()->getDataPath().'/media');
        $this->appendField(new Field\Textarea('emailHtml'))
            ->addCss('mce-med')->setAttr('data-elfinder-path', $this->getCoa()->getProfile()->getInstitution()->getDataPath().'/media')
            ->setAttr('data-background-image', '/~mifsudm/Unimelb/ems/data/media/dvm34PdfBg.png');

        $this->appendField(new Event\Submit('update', array($this, 'doSubmit')));
        $this->appendField(new Event\Submit('save', array($this, 'doSubmit')));
        $this->appendField(new Event\Link('cancel', $this->getBackUrl()));



        $js = <<<JS
jQuery(function ($) {
  
  var ed = $('#coa-html').tinymce();
  ed.on('init', function (e) {
    console.log(this);
    console.log(this.dom.getRoot());
    var body = this.dom.getRoot(); 
    ed.dom.setStyle(body, 'background-image', "url('/~mifsudm/Unimelb/ems/data/media/dvm34PdfBg.png')");
    ed.dom.setStyle(body, 'background-repeat', "no-repeat");
    ed.dom.setStyle(body, 'background-size', "1300px 850px");
    ed.dom.setStyle(body, 'width', "1300px");
    ed.dom.setStyle(body, 'height', "850px");
    ed.dom.setStyle(body, 'background-color', "#EFEFEF");
    
  });
  
  
});
JS;
        $this->getRenderer()->getTemplate()->appendJs($js);



    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function execute($request = null)
    {
        $this->load(\Coa\Db\CoaMap::create()->unmapForm($this->getCoa()));
        parent::execute($request);
    }

    /**
     * @param Form $form
     * @param Event\Iface $event
     * @throws \Exception
     */
    public function doSubmit($form, $event)
    {
        // Load the object with form data
        \Coa\Db\CoaMap::create()->mapForm($form->getValues(), $this->getCoa());

        // Do Custom Validations

        $form->addFieldErrors($this->getCoa()->validate());
        if ($form->hasErrors()) {
            return;
        }
        
        $isNew = (bool)$this->getCoa()->getId();
        $this->getCoa()->save();

        // Do Custom data saving

        \Tk\Alert::addSuccess('Record saved!');
        $event->setRedirect($this->getBackUrl());
        if ($form->getTriggeredEvent()->getName() == 'save') {
            $event->setRedirect(\Tk\Uri::create()->set('coaId', $this->getCoa()->getId()));
        }
    }

    /**
     * @return \Tk\Db\ModelInterface|\Coa\Db\Coa
     */
    public function getCoa()
    {
        return $this->getModel();
    }

    /**
     * @param \Coa\Db\Coa $coa
     * @return $this
     */
    public function setCoa($coa)
    {
        return $this->setModel($coa);
    }
    
}