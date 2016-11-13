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
use Quiz\Storage\HistoryMapperInterface;
use Krystal\Db\Filter\InputDecorator;

final class HistoryMapper extends AbstractMapper implements HistoryMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_quiz_history');
    }

    /**
     * Deletes an item by its id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }

    /**
     * Filters the raw input
     * 
     * @param array|\ArrayAccess $input Raw input data
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page to be displayed
     * @param string $sortingColumn Column name to be sorted
     * @param string $desc Whether to sort in DESC order
     * @return array
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc)
    {
        if (!$sortingColumn) {
            $sortingColumn = 'id';
        }

        $db = $this->db->select('*')
                        ->from(self::getTableName())
                        ->whereEquals('1', '1')
                        ->andWhereEquals('id', $input['id'], true)
                        ->andWhereEquals('category', $input['category'], true)
                        ->andWhereEquals('points', $input['points'], true)
                        ->andWhereLike('name', '%'.$input['name'].'%', true)
                        ->orderBy($sortingColumn);

        if ($desc) {
            $db->desc();
        }

        return $db->paginate($page, $itemsPerPage)
                  ->queryAll();
    }

    /**
     * Fetch all records
     * 
     * @param integer $page
     * @param integer $itemsPerPage
     * @return array
     */
    public function fetchAll($page, $itemsPerPage)
    {
        return $this->db->select('*')
                        ->from(self::getTableName())
                        ->orderBy('id')
                        ->desc()
                        ->paginate($page, $itemsPerPage)
                        ->queryAll();
    }
}
