<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;
use Quiz\Storage\SessionMapperInterface;

final class SessionMapper extends AbstractMapper implements SessionMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_quiz_session');
    }
}
