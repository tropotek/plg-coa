<?php
namespace Coa\Table\Action;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Send extends \Tk\Table\Action\Button
{
    
    /**
     * @var \Tk\Db\Pdo
     */
    protected $db = null;

    /**
     * @var \Coa\Db\Coa
     */
    protected $coa = null;

    /**
     * @var string
     */
    protected $checkboxName = 'id';


    protected $ignoreCellList = array(
        //'Tk\Table\Cell\Checkbox',
        'Tk\Table\Cell\Actions'
    );


    /**
     * @param \Tk\Db\Pdo $db
     * @param string $name
     * @param string $checkboxName
     * @param string $icon
     */
    public function __construct($db, $name = 'send', $checkboxName = 'id', $icon = 'fa fa-send')
    {
        parent::__construct($name, $icon);
        $this->db = $db;
        $this->checkboxName = $checkboxName;
        $this->addCss('tk-action-send btn-no-unload');
    }

    /**
     * @param \Coa\Db\Coa $coa
     * @param string $name
     * @param string $checkboxName
     * @param string $icon
     * @param \Tk\Db\Pdo $db
     * @return Send
     */
    static function create($coa, $name = 'send', $checkboxName = 'id', $icon = 'fa fa-send', $db = null)
    {
        if ($db === null)
            $db = \Tk\Config::getInstance()->getDb();
        $obj = new self($db, $name, $checkboxName, $icon);
        $obj->coa = $coa;
        return $obj;
    }

    /**
     * @return \Coa\Db\Coa
     */
    public function getCoa()
    {
        return $this->coa;
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function execute()
    {
        $request = $this->getTable()->getRequest();
        // Get list with no limit...
        $list = $this->getTable()->getList();
        $fullList = $list;
        if ($request->has($this->checkboxName) && is_array($request->get($this->checkboxName))) {
            $fullList = array();
            foreach($list as $obj) {
                if (in_array($obj->id, $request->get($this->checkboxName)))
                    $fullList[] = $obj;
            }
        } else if ($list && is_object($list) && $list->countAll() > $list->count()) {
            $st = $list->getStatement();
            $sql = $st->queryString;
            if (preg_match('/ LIMIT /i', $sql)) {
                $sql = substr($sql, 0, strrpos($sql, 'LIMIT'));
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($st->getBindParams());
            if ($list->getMapper()) {
                $fullList = \Tk\Db\Map\ArrayObject::createFromMapper($list->getMapper(), $stmt);
            } else {
                $fullList = \Tk\Db\Map\ArrayObject::create($stmt);
            }
        }

        if (!$fullList) {
            \Tk\Log::alert('Empty Send list.');
            return;
        }

        // TODO: -----------------------------------------------------------------------------
        // TODO: This process takes quite a long time when processing over 200 records
        // TODO:   I suggest we build a command execution queue so we can execute jobs
        // TODO:   using a cron command or similar so that it execute in a separate process.
        // TODO: -----------------------------------------------------------------------------
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $success = 0;
        $fail = 0;

        foreach ($fullList as $obj) {
            try {
                $adapter = $this->getCoa()->createPdfAdapter($obj);
                $adapter->replace($this->getTable()->getFilterValues());

                if (!$adapter->get('dateFrom')) {
                    $adapter->set('dateFrom', '01/01/' . date('Y'));
                }
                if (!$adapter->get('dateTo')) {
                    $adapter->set('dateTo', '31/12/' . date('Y'));
                }


                $profile = $this->getCoa()->getProfile();

                // Create message, attachment and email to company
                $message = $this->getConfig()->createMessage();
                $message->setContent($this->getCoa()->emailHtml);

                $message->replace($adapter->all());
                $message->set('sig', $profile->emailSignature);
                $message->set('subject::email', $profile->email);
                $message->set('profile::id', $profile->getId());
                $message->set('profile::email', $profile->email);
                $message->set('profile::name', htmlentities($profile->name));
                $message->addHeader('X-Profile-Name', $profile->name);
                $message->addHeader('X-Profile-ID', $profile->getId());

                $tpl = \Tk\CurlyTemplate::create($this->getCoa()->subject);
                $subject = $tpl->parse($message->all());
                $message->setSubject($subject);
                $message->setFrom($profile->email);
                $message->addTo($adapter->get('email'));

                $pdf = \Coa\Ui\Pdf\Certificate::create($adapter);
                $filename = 'unimelbCert.pdf';
                $message->addStringAttachment($pdf->getPdfAttachment($filename), $filename);

                if ($this->getConfig()->getEmailGateway()->send($message)) {
                    $success++;
                } else {
                    $fail++;
                }
            } catch (\Exception $e) {
                $fail++;
                \Tk\Log::error($adapter->get('name') . ' - ' . $adapter->get('id'));
                \Tk\Log::error($e->__toString());

            }
        }

        $failStr = ' ('.$fail.' Failed)';

        \Tk\Alert::addSuccess($success . ' Certificates send successfully. ' . $failStr);
        \Tk\Uri::create()->redirect();
    }

    /**
     * @return string|\Dom\Template
     */
    public function show()
    {
        $this->setAttr('title', 'Email certificate to selected recipients.');
        $this->setAttr('data-confirm', 'Please Confirm: Send certificates to selected recipients?');

        $template = parent::show();

        return $template;
    }

    /**
     *
     * @param \Tk\Table\Cell\Iface $cell
     * @return array
     */
    private function ignoreCell($cell)
    {
        return in_array(get_class($cell), $this->ignoreCellList);
    }

    /**
     *
     * @param \Tk\Table\Cell\Iface $cell
     * @return $this
     */
    public function addIgnoreCell($cell)
    {
        $this->ignoreCellList[get_class($cell)] = get_class($cell);
        return $this;
    }

    /**
     * Set the ignore cell class array or reset the array if nothing passed
     *
     * Eg:
     *   array('Tk\Table\Cell\Checkbox', 'App\Ui\Subject\EnrolledCell');
     *
     * @param array $array
     * @return $this
     */
    public function setIgnoreCellList($array = array())
    {
        $this->ignoreCellList = $array;
        return $this;
    }

    /**
     * @return \App\Config|\Tk\Config
     */
    public function getConfig()
    {
        return \App\Config::getInstance();
    }

}
