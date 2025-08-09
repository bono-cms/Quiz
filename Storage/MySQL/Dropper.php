<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Storage\MySQL;

use Cms\Storage\MySQL\AbstractStorageDropper;

final class Dropper extends AbstractStorageDropper
{
    /**
     * {@inheritDoc}
     */
    protected function getTables()
    {
        return array(
            AnswerMapper::getTableName(),
            CategoryMapper::getTableName(),
            HistoryMapper::getTableName(),
            QuestionMapper::getTableName(),
            SessionMapper::getTableName(),
            SessionTrackMapper::getTableName()
        );
    }
}
