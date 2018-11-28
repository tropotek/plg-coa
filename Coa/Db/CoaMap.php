<?php
namespace Coa\Db;

use Tk\Db\Tool;
use Tk\Db\Map\ArrayObject;
use Tk\DataMap\Db;
use Tk\DataMap\Form;
use Bs\Db\Mapper;

/**
 * @author Mick Mifsud
 * @created 2018-11-26
 * @link http://tropotek.com.au/
 * @license Copyright 2018 Tropotek
 */
class CoaMap extends Mapper
{

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getDbMap()
    {
        if (!$this->dbMap) { 
            $this->dbMap = new \Tk\DataMap\DataMap();
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Integer('profileId', 'profile_id'));
            $this->dbMap->addPropertyMap(new Db\Text('type'));
            $this->dbMap->addPropertyMap(new Db\Text('background'));
            $this->dbMap->addPropertyMap(new Db\Text('subject'));
            $this->dbMap->addPropertyMap(new Db\Text('html'));
            $this->dbMap->addPropertyMap(new Db\Text('emailHtml', 'email_html'));
            $this->dbMap->addPropertyMap(new Db\Date('modified'));
            $this->dbMap->addPropertyMap(new Db\Date('created'));

        }
        return $this->dbMap;
    }

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getFormMap()
    {
        if (!$this->formMap) {
            $this->formMap = new \Tk\DataMap\DataMap();
            $this->formMap->addPropertyMap(new Form\Integer('id'), 'key');
            $this->formMap->addPropertyMap(new Form\Integer('profileId'));
            $this->formMap->addPropertyMap(new Form\Text('type'));
            $this->formMap->addPropertyMap(new Form\Text('background'));
            $this->formMap->addPropertyMap(new Form\Text('subject'));
            $this->formMap->addPropertyMap(new Form\Text('html'));
            $this->formMap->addPropertyMap(new Form\Text('emailHtml'));
            $this->formMap->addPropertyMap(new Form\Date('modified'));
            $this->formMap->addPropertyMap(new Form\Date('created'));

        }
        return $this->formMap;
    }

    /**
     * @param array $filter
     * @param Tool $tool
     * @return ArrayObject|Coa[]
     * @throws \Exception
     */
    public function findFiltered($filter = array(), $tool = null)
    {
        $this->makeQuery($filter, $tool, $where, $from);
        $res = $this->selectFrom($from, $where, $tool);
        return $res;
    }

    /**
     * @param array $filter
     * @param Tool $tool
     * @param string $where
     * @param string $from
     * @return ArrayObject|Coa[]
     */
    public function makeQuery($filter = array(), $tool = null, &$where = '', &$from = '')
    {
        $from .= sprintf('%s a ', $this->quoteParameter($this->getTable()));

        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->escapeString($filter['keywords']) . '%';
            $w = '';
            //$w .= sprintf('a.name LIKE %s OR ', $this->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) $where .= '(' . substr($w, 0, -3) . ') AND ';

        }

        if (!empty($filter['id'])) {
            $where .= sprintf('a.id = %s AND ', (int)$filter['id']);
        }
        if (!empty($filter['profileId'])) {
            $where .= sprintf('a.profile_id = %s AND ', (int)$filter['profileId']);
        }
        if (!empty($filter['type'])) {
            $where .= sprintf('a.type = %s AND ', $this->quote($filter['type']));
        }
        if (!empty($filter['subject'])) {
            $where .= sprintf('a.subject = %s AND ', $this->quote($filter['subject']));
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $where .= '('. $w . ') AND ';

        }

        if ($where) {
            $where = substr($where, 0, -4);
        }

        return $where;
    }


}
