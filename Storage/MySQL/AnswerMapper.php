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

use Quiz\Storage\AnswerMapperInterface;
use Cms\Storage\MySQL\AbstractMapper;
use Krystal\Db\Sql\RawSqlFragment;

final class AnswerMapper extends AbstractMapper implements AnswerMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_quiz_answers');
    }

    /**
     * Checks whether answer is correct
     * 
     * @param string $questionId
     * @param string $answerId
     * @return boolean
     */
    public function getCorrect($questionId, $answerId)
    {
        $column = 'correct';

        return $this->db->select($column)
                        ->from(self::getTableName())
                        ->whereEquals('question_id', $questionId)
                        ->andWhereEquals('id', $answerId)
                        ->query($column);
    }

    /**
     * Fetch all answers
     * 
     * @param string $id Question id
     * @param boolean $sort Whether to use sorting by order attribute or not
     * @return array
     */
    public function fetchAll($id, $sort)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('question_id', $id);

        if ($sort === true) {
            $db->orderBy(new RawSqlFragment('`order`, CASE WHEN `order` = 0 THEN `id` END DESC'));
        } else {
            $db->orderBy('id')
               ->desc();
        }

        return $db->queryAll();
    }

    /**
     * Fetches an answer by its associated id
     * 
     * @param string $id Answer id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->findByPk($id);
    }

    /**
     * Inserts an answer
     * 
     * @param array $answer
     * @return boolean
     */
    public function insert(array $answer)
    {
        return $this->persist($answer);
    }

    /**
     * Updates an answer
     * 
     * @param array $answer
     * @return boolean
     */
    public function update(array $answer)
    {
        return $this->persist($answer);
    }

    /**
     * Deletes an answer by its associated id
     * 
     * @param string $id Answer id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }
}
