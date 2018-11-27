<?php
namespace Coa\Db;

/**
 * @author Mick Mifsud
 * @created 2018-11-26
 * @link http://tropotek.com.au/
 * @license Copyright 2018 Tropotek
 */
class Coa extends \Tk\Db\Map\Model implements \Tk\ValidInterface
{

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $profileId = 0;

    /**
     * @var string
     */
    public $type = 'company';

    /**
     * @var string
     */
    public $subject = '';

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
     * @var \App\Db\Profile
     */
    private $profile = null;


    /**
     * Coa
     */
    public function __construct()
    {
        $this->modified = new \DateTime();
        $this->created = new \DateTime();

    }

    /**
     * @return \App\Db\Profile|null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface
     * @throws \Exception
     */
    public function getProfile()
    {
        if (!$this->profile) {
            $this->profile = \App\Db\ProfileMap::create()->find($this->profileId);
        }
        return $this->profile;
    }

    /**
     * Get the path for all file associated to this object
     *
     * @return string
     */
    public function getDataPath()
    {
        try {
            return sprintf('%s/coa/%s', $this->getProfile()->getDataPath(), $this->getVolatileId());
        } catch (\Exception $e) { }
        return $this->getDataPath().'/coa/'.$this->getVolatileId();
    }

    /**
     *
     *
     * @return null|\Uni\Uri
     */
    public function getBackgroundUrl()
    {
        if (is_file($this->getConfig()->getDataPath() . $this->background)) {
            return \Uni\Uri::create($this->getConfig()->getDataUrl() . $this->background);
        }
    }

    
    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();


        if (!$this->profileId) {
            $errors['profileId'] = 'Invalid value: profileId';
        }

        if (!$this->type) {
            $errors['type'] = 'Invalid value: type';
        }

        if (!$this->subject) {
            $errors['subject'] = 'Invalid value: subject';
        }

        
        return $errors;
    }

}
