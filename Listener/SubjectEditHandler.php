<?php
namespace Coa\Listener;

use Tk\Event\Subscriber;
use Tk\Event\Event;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class SubjectEditHandler implements Subscriber
{

    /**
     * @var \App\Controller\Subject\Edit
     */
    protected $controller = null;




    /**
     * @param \Tk\Event\ControllerEvent $event
     */
    public function onKernelController(\Tk\Event\ControllerEvent $event)
    {
        /** @var \App\Controller\Subject\Edit $controller */
        $controller = $event->getController();
        if ($controller instanceof \App\Controller\Subject\Edit) {
            $this->controller = $controller;
        }
    }


    /**
     * Check the user has access to this controller
     *
     * @param Event $event
     * @throws \Exception
     */
    public function onControllerInit(Event $event)
    {
        if ($this->controller) {
            if (!$this->controller->getUser()->isStaff() || !$this->controller->getSubject()->getId()) return;
            /** @var \Tk\Ui\Admin\ActionPanel $actionPanel */
            $actionPanel = $this->controller->getActionPanel();
            $actionPanel->add(\Tk\Ui\Button::create('Skill Collections',
                \Uni\Uri::createSubjectUrl('/coaManager.html'), 'fa fa-certificate'));
        }
    }




    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\Kernel\KernelEvents::CONTROLLER => array('onKernelController', 0),
            \Tk\PageEvents::CONTROLLER_INIT => array('onControllerInit', 0)
        );
    }
    
}