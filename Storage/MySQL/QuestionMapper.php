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
use Quiz\Storage\QuestionMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;

final class QuestionMapper extends AbstractMapper implements QuestionMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_quiz_questions');
    }

    /**
     * Inserts a question
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data)
    {
        return $this->persist($data);
    }

    /**
     * Updates a question
     * 
     * @param array $data
     * @return boolean
     */
    public function update(array $data)
    {
        return $this->persist($data);
    }

    /**
     * Updates sorting order by id
     * 
     * @param string $id
     * @param string $order
     * @return boolean
     */
    public function updateOrderById($id, $order)
    {
        return $this->updateColumnByPk($id, 'order', $order);
    }

    /**
     * Fetches question title by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchQuestionById($id)
    {
        return $this->findColumnByPk($id, 'question');
    }

    /**
     * Fetches question data by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->findByPk($id);
    }

    /**
     * Deletes a question by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }

    /**
     * Deletes all questions associated with category id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteAllByCategoryId($id)
    {
        return $this->deleteByColumn('category_id', $id);
    }

    /**
     * Counts amount of questions by associated category id
     * 
     * @param string $id Category id
     * @return integer
     */
    public function countAllByCategoryId($id)
    {
        $alias = 'count';

        return $this->db->select()
                        ->count('id', $alias)
                        ->from(self::getTableName())
                        ->whereEquals('category_id', $id)
                        ->query($alias);
    }

    /**
     * Fetches question ids by associated category id
     * 
     * @param string $id Category id
     * @return array
     */
    public function fetchQuiestionIdsByCategoryId($id)
    {
        return $this->db->select('id')
                        ->from(self::getTableName())
                        ->whereEquals('category_id', $id)
                        ->orderBy(new RawSqlFragment('`order`, CASE WHEN `order` = 0 THEN `id` END DESC'))
                        ->queryAll('id');
    }

    /**
     * Fetches all answer entities associated with category id
     * 
     * @param string $id Category id
     * @param integer $page Current page number
     * @param integer $itemPerPage Per page count
     * @return array
     */
    public function fetchAllByCategoryId($id, $page, $itemsPerPage)
    {
        return $this->db->select('*')
                        ->from(self::getTableName())
                        ->whereEquals('category_id', $id)
                        ->orderBy('id')
                        ->desc()
                        ->paginate($page, $itemsPerPage)
                        ->queryAll();
    }
}
