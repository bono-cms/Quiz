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

use Krystal\Config\ConfigModuleService;
use Krystal\Stdlib\VirtualEntity;

final class ConfigManager extends ConfigModuleService
{
    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        $entity = new VirtualEntity;
        $entity->setSortingMethod($this->get('order_type', '1'), VirtualEntity::FILTER_INT);

        return $entity;
    }
}
