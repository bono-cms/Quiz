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

use Quiz\Storage\HistoryMapperInterface;

final class HistoryService implements HistoryServiceInterface
{
    /**
     * History mapper
     * 
     * @var \Quiz\Storage\HistoryMapperInterface
     */
    private $historyMapper;

    /**
     * State initialization
     * 
     * @param \Quiz\Storage\HistoryMapperInterface $historyMapper
     * @return void
     */
    public function __construct(HistoryMapperInterface $historyMapper)
    {
        $this->historyMapper = $historyMapper;
    }
}
