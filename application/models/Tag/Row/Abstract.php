<?php

/**
 * Row definition class for table tag.
 *
 * Do NOT write anything in this file, it will be removed when you regenerated.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 * @method Application_Model_Tag_Row setFromArray($data)
 *
 * @property mixed $id
 * @property mixed $name
 * @property mixed $post_count
 */
abstract class Application_Model_Tag_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
    /**
     * Set value for 'id' field
     *
     * @param mixed $Id
     *
     * @return Application_Model_Tag_Row
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
     * @return Application_Model_Tag_Row
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
     * Set value for 'post_count' field
     *
     * @param mixed $PostCount
     *
     * @return Application_Model_Tag_Row
     */
    public function setPostCount($PostCount)
    {
        $this->post_count = $PostCount;
        return $this;
    }

    /**
     * Get value of 'post_count' field
     *
     * @return mixed
     */
    public function getPostCount()
    {
        return $this->post_count;
    }

    /**
     * Get a list of rows of Post.
     *
     * @return Application_Model_Post_Rowset
     */
    public function getPostRowset()
    {
        return $this->findManyToManyRowset('Application_Model_Post_DbTable', 'Application_Model_PostsTags_DbTable', 'post_id');
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
