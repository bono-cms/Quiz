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

interface HistoryServiceInterface
{
    /**
     * Returns all records
     * 
     * @param integer $page
     * @param integer $itemsPerPage
     * @return array
     */
    public function fetchAll($page, $itemsPerPage);

    /**
     * Returns prepared paginator instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator();

    /**
     * Tracks the history
     * 
     * @param array $data
     * @return boolean
     */
    public function track(array $data);
}
