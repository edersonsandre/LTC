<?php

/**
 * Data mapper class for table category.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 */
class Application_Model_CategoryMapper
{
    /**
     *
     * @var Application_Model_Category_DbTable
     */
    protected $_dbTable;

    public function __construct()
    {
        $this->_dbTable = new Application_Model_Category_DbTable();
    }

    /**
     *
     * @return Application_Model_Category_DbTable
     */
    public function getDbTabe()
    {
        return $this->_dbTable;
    }
}
