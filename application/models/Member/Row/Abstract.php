<?php

/**
 * Row definition class for table member.
 *
 * Do NOT write anything in this file, it will be removed when you regenerated.
 *
 * @package Zodekentest
 * @author Zodeken
 * @version $Id$
 *
 * @method Application_Model_Member_Row setFromArray($data)
 *
 * @property mixed $id
 * @property mixed $email
 * @property mixed $gender
 * @property mixed $date_registered
 */
abstract class Application_Model_Member_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
    /**
     * Set value for 'id' field
     *
     * @param mixed $Id
     *
     * @return Application_Model_Member_Row
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
     * Set value for 'email' field
     *
     * @param mixed $Email
     *
     * @return Application_Model_Member_Row
     */
    public function setEmail($Email)
    {
        $this->email = $Email;
        return $this;
    }

    /**
     * Get value of 'email' field
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set value for 'gender' field
     *
     * @param mixed $Gender
     *
     * @return Application_Model_Member_Row
     */
    public function setGender($Gender)
    {
        $this->gender = $Gender;
        return $this;
    }

    /**
     * Get value of 'gender' field
     *
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set value for 'date_registered' field
     *
     * @param mixed $DateRegistered
     *
     * @return Application_Model_Member_Row
     */
    public function setDateRegistered($DateRegistered)
    {
        $this->date_registered = $DateRegistered;
        return $this;
    }

    /**
     * Get value of 'date_registered' field
     *
     * @return mixed
     */
    public function getDateRegistered()
    {
        return $this->date_registered;
    }

    /**
     * Get a list of rows of Post.
     *
     * @return Application_Model_Post_Rowset
     */
    public function getPostRowsByOwnerId()
    {
        return $this->findDependentRowset('Application_Model_Post_DbTable', 'owner_id');
    }
    
    /**
     * Get the label that has been auto-detected by Zodeken
     *
     * @return string
     */
    public function getZodekenAutoLabel()
    {
        return $this->email;
    }
}
