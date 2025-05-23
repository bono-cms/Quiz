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
use Quiz\Storage\SessionTrackMapperInterface;

final class SessionTrackMapper extends AbstractMapper implements SessionTrackMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_quiz_session_track');
    }

    /**
     * Updates a track with answers
     * 
     * @param int $id Track id
     * @param string $answers JSON string for answers
     * @return boolean
     */
    public function updateTrack($id, $answers)
    {
        $db = $this->db->update(self::getTableName(), ['answers' => $answers])
                       ->whereEquals('id', $id);

        return $db->execute();
    }

    /**
     * Fetch all tracked items
     * 
     * @param int $sessionId
     * @return array
     */
    public function fetchAll($sessionId)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('session_id', $sessionId);

        return $db->queryAll();
    }
}
