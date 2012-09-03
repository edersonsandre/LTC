<?php

/**
 * Row definition class for table category.
 *
 * Do NOT write anything in this file, it will be removed when you regenerated.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 * @method Application_Model_Category_Row setFromArray($data)
 *
 * @property mixed $id
 * @property mixed $name
 * @property mixed $sort_order
 */
abstract class Application_Model_Category_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
    /**
     * Set value for 'id' field
     *
     * @param mixed $Id
     *
     * @return Application_Model_Category_Row
     */
    public function setId($Id)
    {
        $this->id = $Id;
        return $this;
    }

    /**
     * Get value of 'id' field
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value for 'name' field
     *
     * @param mixed $Name
     *
     * @return Application_Model_Category_Row
     */
    public function setName($Name)
    {
        $this->name = $Name;
        return $this;
    }

    /**
     * Get value of 'name' field
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value for 'sort_order' field
     *
     * @param mixed $SortOrder
     *
     * @return Application_Model_Category_Row
     */
    public function setSortOrder($SortOrder)
    {
        $this->sort_order = $SortOrder;
        return $this;
    }

    /**
     * Get value of 'sort_order' field
     *
     * @return mixed
     */
    public function getSortOrder()
    {
        return $this->sort_order;
    }

    /**
     * Get a list of rows of Post.
     *
     * @return Application_Model_Post_Rowset
     */
    public function getPostRowsByCategoryId()
    {
        return $this->findDependentRowset('Application_Model_Post_DbTable', 'category_id');
    }
    
    /**
     * Get the label that has been auto-detected by Zodeken
     *
     * @return string
     */
    public function getZodekenAutoLabel()
    {
        return $this->name;
    }
}
