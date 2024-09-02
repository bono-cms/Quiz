<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Storage;

interface CategoryMapperInterface
{
    /**
     * Used purely for displaying results
     * 
     * @param array $questionIds An array of passed questions ids
     * @return array
     */
    public function fetchResultset(array $questionIds);

    /**
     * Fetch all categories
     * 
     * @param boolean $sort Whether to use sorting by order attribute or not
     * @param boolean $empty Whether to fetch empty categories as well
     * @return array
     */
    public function fetchAll($sort, $empty = true);

    /**
     * Deletes a category by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);
    
    /**
     * Finds a category by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id);

    /**
     * Fetch category name by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id);

    /**
     * Inserts a category
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data);

    /**
     * Updates a category
     * 
     * @param array $data
     * @return boolean
     */
    public function update(array $data);
}
