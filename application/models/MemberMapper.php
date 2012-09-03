<?php

/**
 * Data mapper class for table member.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 */
class Application_Model_MemberMapper
{
    /**
     *
     * @var Application_Model_Member_DbTable
     */
    protected $_dbTable;

    public function __construct()
    {
        $this->_dbTable = new Application_Model_Member_DbTable();
    }

    /**
     *
     * @return Application_Model_Member_DbTable
     */
    public function getDbTabe()
    {
        return $this->_dbTable;
    }
}
