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

interface QuestionServiceInterface
{
    /**
     * Returns prepared pagination instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator();

    /**
     * Fetches all questions entities associated with category id
     * 
     * @param string $id Category id
     * @param integer $page Current page number
     * @param integer $itemPerPage Per page count
     * @return array
     */
    public function fetchAllByCategoryId($id, $page, $itemsPerPage);

    /**
     * Fetches question entity by its associate id
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id);

    /**
     * Deletes a question by its associate di
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Returns last question id
     * 
     * @return string
     */
    public function getLastId();

    /**
     * Adds a question
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input);

    /**
     * Updates a question
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input);
}
