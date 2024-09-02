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

interface HistoryMapperInterface
{
    /**
     * Fetch history item by its id
     * 
     * @param string $slug
     * @return array
     */
    public function fetchBySlug($slug);

    /**
     * Deletes an item by its id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Filters the raw input
     * 
     * @param array|\ArrayAccess $input Raw input data
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page to be displayed
     * @param string $sortingColumn Column name to be sorted
     * @param string $desc Whether to sort in DESC order
     * @return array
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc);

    /**
     * Fetch all records
     * 
     * @param integer $page
     * @param integer $itemsPerPage
     * @return array
     */
    public function fetchAll($page, $itemsPerPage);
}
