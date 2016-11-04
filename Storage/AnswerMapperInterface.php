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

interface AnswerMapperInterface
{
    /**
     * Checks whether answer is correct
     * 
     * @param string $questionId
     * @param string $answerId
     * @return boolean
     */
    public function getCorrect($questionId, $answerId);

    /**
     * Fetch all answers
     * 
     * @param string $id Question id
     * @param boolean $sort Whether to use sorting by order attribute or not
     * @return array
     */
    public function fetchAll($id, $sort);

    /**
     * Fetches an answer by its associated id
     * 
     * @param string $id Answer id
     * @return array
     */
    public function fetchById($id);

    /**
     * Inserts an answer
     * 
     * @param array $answer
     * @return boolean
     */
    public function insert(array $answer);

    /**
     * Updates an answer
     * 
     * @param array $answer
     * @return boolean
     */
    public function update(array $answer);

    /**
     * Deletes an answer by its associated id
     * 
     * @param string $id Answer id
     * @return boolean
     */
    public function deleteById($id);
}
