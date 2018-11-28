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

        $tab = 'Details';
        
        $list = array('Company' => 'company', 'Staff' => 'staff', 'Student' => 'student');
        $this->appendField(new Field\Select('type', $list))->prependOption('-- Select --', '')->setTabGroup($tab)
            ->setNotes('Select the user type that this certificate will be sent to.');
        //$this->appendField(new Field\Input('type'));
        $this->appendField(new Field\Input('subject'))->setTabGroup($tab);

        $this->addField(new Field\File('background', $this->getCoa()->getDataPath()))->setTabGroup($tab)
            ->setMaxFileSize($this->getConfig()->get('upload.profile.imagesize'))->setAttr('accept', '.png,.jpg,.jpeg,.gif')
            ->addCss('tk-imageinput')
            ->setNotes('Upload the background image for the certificate (Recommended Size: 1300x850). Note: Save the record after selecting the image so it is applied to the certificate template in the next tab.');

        $tab = 'Certificate';
        $htmlEl = $this->appendField(new Field\Textarea('html'))->setTabGroup($tab)
            ->addCss('mce')->setAttr('data-elfinder-path', $this->getCoa()->getProfile()->getInstitution()->getDataPath().'/media');
        if ($this->getCoa()->getBackgroundUrl()) {
            $htmlEl->setAttr('data-background-image', $this->getCoa()->getBackgroundUrl());
        }

        $tab = 'Email Template';
        $this->appendField(new Field\Textarea('emailHtml'))->setTabGroup($tab)
            ->addCss('mce-med')->setAttr('data-elfinder-path', $this->getCoa()->getProfile()->getInstitution()->getDataPath().'/media');

        $this->appendField(new Event\Submit('update', array($this, 'doSubmit')));
        $this->appendField(new Event\Submit('save', array($this, 'doSubmit')));
        $this->appendField(new Event\Link('cancel', $this->getBackUrl()));


        $js = <<<JS
jQuery(function ($) {
  // Add background image to the <body> tag of the MCE editor
  $('#coa-html').tinymce().on('init', function (e) {
    var body = this.dom.getRoot(); 
    this.dom.setStyle(body, 'background-image', "url('"+$(this.targetElm).data('backgroundImage')+"')");
    this.dom.setStyle(body, 'background-repeat', "no-repeat");
    this.dom.setStyle(body, 'background-size', "1300px 850px");
    this.dom.setStyle(body, 'width', "1300px");
    this.dom.setStyle(body, 'height', "850px");
    this.dom.setStyle(body, 'background-color', "#EFEFEF");
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

        /** @var \Tk\Form\Field\File $image */
        $image = $form->getField('background');

        // Do Custom Validations
        if ($image->hasFile() && !preg_match('/\.(gif|jpe?g|png)$/i', $image->getValue())) {
            $form->addFieldError('background', 'Please Select a valid image file. (jpg, png, gif)');
        }

        $form->addFieldErrors($this->getCoa()->validate());
        if ($form->hasErrors()) {
            return;
        }
        
        $isNew = (bool)$this->getCoa()->getId();
        $this->getCoa()->save();

        $image->saveFile();


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