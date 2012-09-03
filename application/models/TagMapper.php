<?php

/**
 * Data mapper class for table tag.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 */
class Application_Model_TagMapper
{
    /**
     *
     * @var Application_Model_Tag_DbTable
     */
    protected $_dbTable;

    public function __construct()
    {
        $this->_dbTable = new Application_Model_Tag_DbTable();
    }

    /**
     *
     * @return Application_Model_Tag_DbTable
     */
    public function getDbTabe()
    {
        return $this->_dbTable;
    }
}
