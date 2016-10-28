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

interface QuestionMapperInterface
{
    /**
     * Inserts a question
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data);

    /**
     * Updates a question
     * 
     * @param array $data
     * @return boolean
     */
    public function update(array $data);

    /**
     * Fetches question data by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id);

    /**
     * Deletes a question by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Deletes all questions associated with category id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteAllByCategoryId($id);

    /**
     * Counts amount of questions by associated category id
     * 
     * @param string $id Category id
     * @return integer
     */
    public function countAllByCategoryId($id);

    /**
     * Fetches all answer entities associated with category id
     * 
     * @param string $id Category id
     * @param integer $page Current page number
     * @param integer $itemPerPage Per page count
     * @return array
     */
    public function fetchAllByCategoryId($id, $page, $itemsPerPage);
}
