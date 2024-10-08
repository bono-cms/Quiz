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

final class ConfigManager extends ConfigModuleService
{
    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        $entity = new ConfigEntity;
        $entity->setSortingMethod($this->get('order_type', 'order'), ConfigEntity::FILTER_TAGS)
               ->setLimit($this->get('limit'))
               ->setContinue($this->get('continue'), '0');

        return $entity;
    }
}
