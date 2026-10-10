<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Controller\Admin;

use Krystal\Validation\Validator;
use Cms\Controller\Admin\AbstractConfigController;

final class Config extends AbstractConfigController
{
    /**
     * {@inheritDoc}
     */
    protected $parent = 'Quiz:Admin:Browser@indexAction';

    /**
     * {@inheritDoc}
     */
    protected function configureValidator(Validator $validator)
    {
        // No validation rules for this module yet
    }
}
