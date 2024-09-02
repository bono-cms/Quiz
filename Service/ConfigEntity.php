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
    const PARAM_SORT_TYPE_ORDER = 'order';
    const PARAM_SORT_TYPE_RANDOM = 'rand';

    /**
     * Whether should stop and diplay results on finish
     * 
     * @return boolean
     */
    public function shouldStop()
    {
        return $this->getContinue() == '0';
    }

    /**
     * Returns supported continue types
     * 
     * @return array
     */
    public function getContinueTypes()
    {
        return array(
            '0' => 'Stop and display results',
            '1' => 'Allow going to the next category'
        );
    }

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
