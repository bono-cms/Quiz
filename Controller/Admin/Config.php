<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Controller\Admin;

use Cms\Controller\Admin\AbstractConfigController;
use Krystal\Validate\Pattern;

final class Config extends AbstractConfigController
{
    /**
     * {@inheritDoc}
     */
    protected $parent = 'Quiz:Admin:Browser@indexAction';

    /**
     * {@inheritDoc}
     */
    protected function getValidationRules()
    {
        return array();
    }
}
