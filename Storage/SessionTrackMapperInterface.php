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

interface SessionTrackMapperInterface
{
    /**
     * Fetch all tracked items
     * 
     * @param int $sessionId
     * @return array
     */
    public function fetchAll($sessionId);
}
