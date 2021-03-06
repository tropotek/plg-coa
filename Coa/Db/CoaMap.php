<?php
namespace Coa\Db;

use Bs\Db\Mapper;
use Tk\DataMap\Db;
use Tk\DataMap\Form;
use Tk\Db\Map\ArrayObject;
use Tk\Db\Tool;

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
            $this->dbMap->addPropertyMap(new Db\Integer('courseId', 'course_id'));
            $this->dbMap->addPropertyMap(new Db\Text('type'));
            $this->dbMap->addPropertyMap(new Db\Text('background'));
            $this->dbMap->addPropertyMap(new Db\Text('msgSubject', 'subject'));
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
            $this->formMap->addPropertyMap(new Form\Integer('courseId'));
            $this->formMap->addPropertyMap(new Form\Text('type'));
            $this->formMap->addPropertyMap(new Form\Text('background'));
            $this->formMap->addPropertyMap(new Form\Text('msgSubject'));
            $this->formMap->addPropertyMap(new Form\Text('html'));
            $this->formMap->addPropertyMap(new Form\Text('emailHtml'));
            $this->formMap->addPropertyMap(new Form\Date('modified'));
            $this->formMap->addPropertyMap(new Form\Date('created'));

        }
        return $this->formMap;
    }
    /**
     * @param array|\Tk\Db\Filter $filter
     * @param Tool $tool
     * @return ArrayObject|Coa[]
     * @throws \Exception
     */
    public function findFiltered($filter, $tool = null)
    {
        return $this->selectFromFilter($this->makeQuery(\Tk\Db\Filter::create($filter)), $tool);
    }

    /**
     * @param \Tk\Db\Filter $filter
     * @return \Tk\Db\Filter
     */
    public function makeQuery(\Tk\Db\Filter $filter)
    {
        $filter->appendFrom('%s a ', $this->quoteParameter($this->getTable()));

        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
            $w = '';
            //$w .= sprintf('a.name LIKE %s OR ', $this->getDb()->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) $filter->appendWhere('(%s) AND ', substr($w, 0, -3));
        }


        if (!empty($filter['id'])) {
            $w = $this->makeMultiQuery($filter['id'], 'a.id');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }
        if (!empty($filter['courseId'])) {
            $filter->appendWhere('a.course_id = %s AND ', (int)$filter['courseId']);
        }
        if (!empty($filter['type'])) {
            $filter->appendWhere('a.type = %s AND ', $this->quote($filter['type']));
        }
        if (!empty($filter['msgSubject'])) {
            $filter->appendWhere('a.subject = %s AND ', $this->quote($filter['msgSubject']));
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        return $filter;
    }

}
