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
use Quiz\Storage\CategoryMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;

final class CategoryMapper extends AbstractMapper implements CategoryMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_quiz_categories');
    }

    /**
     * Used purely for displaying results
     * 
     * @param array $questionIds An array of passed questions ids
     * @return array
     */
    public function fetchResultset(array $questionIds)
    {
        // Hack to avoid SQL errors when empty set supplied
        if (empty($questionIds)) {
            $questionIds = [0];
        }

        $columns = [
            self::column('id'),
            self::column('name'),
            self::column('mark')
        ];

        $db = $this->db->select($columns)
                       ->count(QuestionMapper::column('id'), 'correct')
                       ->from(self::getTableName())
                       ->leftJoin(QuestionMapper::getTableName(), [
                            QuestionMapper::column('category_id') => self::getRawColumn('id')
                       ])
                       ->rawAnd()
                       ->in(QuestionMapper::column('id'), $questionIds)
                       ->groupBy($columns)
                       ->orderBy(self::column('order'));

        return $db->queryAll();
    }

    /**
     * Fetch all categories
     * 
     * @param boolean $sort Whether to use sorting by order attribute or not
     * @param boolean $empty Whether to fetch empty categories as well
     * @return array
     */
    public function fetchAll($sort, $empty = true)
    {
        // Columns to be selected
        $columns = [
            self::column('id'),
            self::column('name'),
            self::column('order'),
            self::column('mark')
        ];

        $db = $this->db->select($columns)
                       ->count(QuestionMapper::column('id'), 'count')
                       ->from(self::getTableName())
                       ->join(!$empty ? 'RIGHT' : 'LEFT', QuestionMapper::getTableName(), [
                            QuestionMapper::column('category_id') => self::getRawColumn('id')
                       ])
                       ->whereEquals(self::column('lang_id'), $this->getLangId())
                       ->groupBy($columns);

        $colOrder = self::column('order');
        $colId = self::column('id');

        if ($sort === true) {
            $db->orderBy(new RawSqlFragment(sprintf('%s, CASE WHEN %s = 0 THEN %s END DESC', $colOrder, $colOrder, $colId)));
        } else {
            $db->orderBy($colId)
               ->desc();
        }

        return $db->queryAll();
    }

    /**
     * Deletes a category by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }

    /**
     * Finds a category by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->findByPk($id);
    }

    /**
     * Fetch category name by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id)
    {
        return $this->findColumnByPk($id, 'name');
    }

    /**
     * Inserts a category
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data)
    {
        return $this->persist($this->getWithLang($data));
    }

    /**
     * Updates a category
     * 
     * @param array $data
     * @return boolean
     */
    public function update(array $data)
    {
        return $this->persist($data);
    }
}
