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
     * Updates sorting order by id
     * 
     * @param string $id
     * @param string $order
     * @return boolean
     */
    public function updateOrderById($id, $order);

    /**
     * Fetches question title by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchQuestionById($id);

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
     * Count amount of questions by category id
     * 
     * @param int $categoryId Category id
     * @param bool $limit Optinal limit
     * @return int
     */
    public function countQuestionsByCategoryId($categoryId, $limit = null);

    /**
     * Fetch random question id by category id
     * 
     * @param int $categoryId Category id
     * @param array $excludedIds Ids to be excluded
     * @return int
     */
    public function fetchRandomQuestionIdByCategoryId($categoryId, array $excludedIds = array());

    /**
     * Fetches next question ids by associated category id
     * 
     * @param int $categoryId Category id
     * @param bool $sort Whether to enable sorting by order
     * @param int $current Current number
     * @return int Question id
     */
    public function fetchQuiestionIdByCategoryId($categoryId, $sort, $current);

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
