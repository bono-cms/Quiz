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

use Krystal\Db\Sql\QueryBuilder;
use Krystal\Db\Sql\RawSqlFragment;
use Cms\Storage\MySQL\AbstractMapper;
use Quiz\Storage\QuestionMapperInterface;

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
     * Count amount of questions by category id
     * 
     * @param int $categoryId Category id
     * @param bool $limit Optinal limit
     * @return int
     */
    public function countQuestionsByCategoryId($categoryId, $limit = null)
    {
        // Case: No restriction when couting
        if ($limit == null) {
            $db = $this->db->select()
                           ->count('id')
                           ->from(self::getTableName())
                           ->whereEquals('category_id', $categoryId);
        } else {
            // Case: Restricted when couting up to $limit
            $qb = new QueryBuilder();
            $qb->openBracket()
               ->select('id')
               ->from(self::getTableName())
               ->whereEquals('category_id', (int) $categoryId)
               ->limit($limit)
               ->closeBracket();

            $db = $this->db->select()
                           ->count('id')
                           ->from($qb->getQueryString())
                           ->asAlias('alias');
        }

        return (int) $db->queryScalar();
    }

    /**
     * Fetch random question id by category id
     * 
     * @param int $categoryId Category id
     * @param array $excludedIds Ids to be excluded
     * @return int
     */
    public function fetchRandomQuestionIdByCategoryId($categoryId, array $excludedIds = array())
    {
        $db = $this->db->select('id')
                       ->from(self::getTableName())
                       ->whereEquals('category_id', $categoryId)
                       ->andWhereNotIn('id', $excludedIds)
                       ->orderBy()
                       ->rand()
                       ->limit(1);

        return $db->queryScalar();
    }

    /**
     * Fetches next question ids by associated category id
     * 
     * @param int $categoryId Category id
     * @param bool $sort Whether to enable sorting by order
     * @param int $current Current number
     * @return int Question id
     */
    public function fetchQuiestionIdByCategoryId($categoryId, $sort, $current)
    {
        $db = $this->db->select('id')
                       ->from(self::getTableName())
                       ->whereEquals('category_id', $categoryId);

        if ($sort) {
            $db->orderBy(new RawSqlFragment('`order`, CASE WHEN `order` = 0 THEN `id` END DESC'));
        } else {
            $db->orderBy('id')
               ->desc();
        }

        $limit = 1;
        $offset = ($current - 1) * $limit;

        $db->limit($offset, $limit);

        return $db->queryScalar();
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
