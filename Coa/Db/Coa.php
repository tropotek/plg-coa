<?php
namespace Coa\Db;

use Bs\Db\Traits\TimestampTrait;
use Uni\Db\Traits\CourseTrait;

/**
 * @author Mick Mifsud
 * @created 2018-11-26
 * @link http://tropotek.com.au/
 * @license Copyright 2018 Tropotek
 */
class Coa extends \Tk\Db\Map\Model implements \Tk\ValidInterface
{

    use CourseTrait;
    use TimestampTrait;

    const TYPE_SUPERVISOR   = 'supervisor';
    const TYPE_COMPANY      = 'company';
    const TYPE_STAFF        = 'staff';
    const TYPE_STUDENT      = 'student';

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $courseId = 0;

    /**
     * @var string
     */
    public $type = self::TYPE_SUPERVISOR;

    /**
     * @var string
     */
    public $msgSubject = '';

    /**
     * @var string
     */
    public $background = '';

    /**
     * @var string
     */
    public $html = '';

    /**
     * @var string
     */
    public $emailHtml = '';

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var \DateTime
     */
    public $created = null;


    /**
     * Coa
     */
    public function __construct()
    {
        $this->_TimestampTrait();

    }

    /**
     * Get the path for all file associated to this object
     *
     * @return string
     */
    public function getDataPath()
    {
        try {
            return sprintf('%s/coa/%s', $this->getCourse()->getDataPath(), $this->getVolatileId());
        } catch (\Exception $e) { }
        return $this->getDataPath().'/coa/'.$this->getVolatileId();
    }

    /**
     * @return null|\Uni\Uri
     */
    public function getBackgroundUrl()
    {
        $url = null;
        if (is_file($this->getConfig()->getDataPath() . $this->getBackground())) {
            $url = \Uni\Uri::create($this->getConfig()->getDataUrl() . $this->getBackground());
        }
        return $url;
    }

    /**
     * @param \Tk\Db\Map\Model|\Tk\Db\ModelInterface $model
     * @return \Coa\Adapter\Company|\Coa\Adapter\Supervisor|\Coa\Adapter\User
     * @throws \Tk\Exception
     */
    public function createPdfAdapter($model)
    {
        switch ($this->type) {
            case self::TYPE_SUPERVISOR:
                $adapter = new \Coa\Adapter\Supervisor($this, $model);
                break;
            case self::TYPE_COMPANY:
                $adapter = new \Coa\Adapter\Company($this, $model);
                break;
            case self::TYPE_STAFF:
            case self::TYPE_STUDENT:
                $adapter = new \Coa\Adapter\User($this, $model);
                $adapter = new \Coa\Adapter\User($this, $model);
                break;
        }
        return $adapter;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Coa
     */
    public function setType(string $type): Coa
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getMsgSubject(): string
    {
        return $this->msgSubject;
    }

    /**
     * @param string $msgSubject
     * @return Coa
     */
    public function setMsgSubject(string $msgSubject): Coa
    {
        $this->msgSubject = $msgSubject;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackground(): string
    {
        return $this->background;
    }

    /**
     * @param string $background
     * @return Coa
     */
    public function setBackground(string $background): Coa
    {
        $this->background = $background;
        return $this;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @param string $html
     * @return Coa
     */
    public function setHtml(string $html): Coa
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailHtml(): string
    {
        return $this->emailHtml;
    }

    /**
     * @param string $emailHtml
     * @return Coa
     */
    public function setEmailHtml(string $emailHtml): Coa
    {
        $this->emailHtml = $emailHtml;
        return $this;
    }

    
    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();
        $errors = $this->validateCourseId($errors);

        // TODO: Check type exists in contants
        if (!$this->type) {
            $errors['type'] = 'Invalid value: type';
        }

        if (!$this->msgSubject) {
            $errors['subject'] = 'Invalid value: subject';
        }

        
        return $errors;
    }

}
