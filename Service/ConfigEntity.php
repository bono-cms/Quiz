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

use Krystal\Stdlib\VirtualEntity;

final class ConfigEntity extends VirtualEntity
{
    const PARAM_SORT_TYPE_ORDER = 1;
    const PARAM_SORT_TYPE_RANDOM = 2;

    /**
     * Returns sorting types
     * 
     * @return array
     */
    public function getSortingTypes()
    {
        return array(
            self::PARAM_SORT_TYPE_ORDER => 'Sort by sorting number',
            self::PARAM_SORT_TYPE_RANDOM => 'Sort randomly'
        );
    }

    /**
     * Whether to sort by ordering number
     * 
     * @return boolean
     */
    public function sortByOrder()
    {
        return $this->getSortingMethod() == self::PARAM_SORT_TYPE_ORDER;
    }

    /**
     * Whether to sort randomly
     * 
     * @return boolean
     */
    public function sortByRand()
    {
        return $this->getSortingMethod() == self::PARAM_SORT_TYPE_RANDOM;
    }
}
