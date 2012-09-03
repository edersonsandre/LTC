<?php

/**
 * Data mapper class for table post.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 */
class Application_Model_PostMapper
{
    /**
     *
     * @var Application_Model_Post_DbTable
     */
    protected $_dbTable;

    public function __construct()
    {
        $this->_dbTable = new Application_Model_Post_DbTable();
    }

    /**
     *
     * @return Application_Model_Post_DbTable
     */
    public function getDbTabe()
    {
        return $this->_dbTable;
    }
}
