<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Service;

Interface AnswerServiceInterface
{
    /**
     * Checks whether there only one or several correct answers
     * 
     * @param array $entities
     * @return boolean
     */
    public function hasManyCorrectAnswers(array $entities);

    /**
     * Returns last answer id
     * 
     * @return integer
     */
    public function getLastId();
    
    /**
     * Adds an answer
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input);

    /**
     * Updates an answer
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input);

    /**
     * Deletes an answer by its id
     * 
     * @param string $id Answer id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Fetch all answers
     * 
     * @param string $id Question id
     * @param boolean $sort Whether to use sorting by order attribute or not
     * @return array
     */
    public function fetchAll($id, $sort);

    /**
     * Fetches an answer by its id
     * 
     * @param string $id Answer id
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id);
}
