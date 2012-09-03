<?php

/**
 * Row definition class for table posts_tags.
 *
 * Do NOT write anything in this file, it will be removed when you regenerated.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 * @method Application_Model_PostsTags_Row setFromArray($data)
 *
 * @property mixed $post_id
 * @property mixed $tag_id
 */
abstract class Application_Model_PostsTags_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
    /**
     * Set value for 'post_id' field
     *
     * @param mixed $PostId
     *
     * @return Application_Model_PostsTags_Row
     */
    public function setPostId($PostId)
    {
        $this->post_id = $PostId;
        return $this;
    }

    /**
     * Get value of 'post_id' field
     *
     * @return mixed
     */
    public function getPostId()
    {
        return $this->post_id;
    }

    /**
     * Set value for 'tag_id' field
     *
     * @param mixed $TagId
     *
     * @return Application_Model_PostsTags_Row
     */
    public function setTagId($TagId)
    {
        $this->tag_id = $TagId;
        return $this;
    }

    /**
     * Get value of 'tag_id' field
     *
     * @return mixed
     */
    public function getTagId()
    {
        return $this->tag_id;
    }

    /**
     * Get a row of Post.
     *
     * @return Application_Model_Post_Row
     */
    public function getPostRowByPostId()
    {
        return $this->findParentRow('Application_Model_Post_DbTable', 'post_id');
    }

    /**
     * Get a row of Tag.
     *
     * @return Application_Model_Tag_Row
     */
    public function getTagRowByTagId()
    {
        return $this->findParentRow('Application_Model_Tag_DbTable', 'tag_id');
    }
    
    /**
     * Get the label that has been auto-detected by Zodeken
     *
     * @return string
     */
    public function getZodekenAutoLabel()
    {
        return $this->post_id;
    }
}
