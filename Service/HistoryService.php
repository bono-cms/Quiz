<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Service;

use Quiz\Storage\HistoryMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Db\Filter\FilterableServiceInterface;

final class HistoryService extends AbstractManager implements FilterableServiceInterface
{
    /**
     * History mapper
     * 
     * @var \Quiz\Storage\HistoryMapperInterface
     */
    private $historyMapper;

    /**
     * State initialization
     * 
     * @param \Quiz\Storage\HistoryMapperInterface $historyMapper
     * @return void
     */
    public function __construct(HistoryMapperInterface $historyMapper)
    {
        $this->historyMapper = $historyMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setName($row['name'])
               ->setCategory($row['category'])
               ->setPoints($row['points'])
               ->setDate(date('Y-m-d H:i:s', $row['timestamp']));

        return $entity;
    }

    /**
     * Fetch history item by its id
     * 
     * @param string $slug
     * @return array
     */
    public function fetchBySlug($slug)
    {
        $row = $this->historyMapper->fetchBySlug($slug);

        if ($row) {
            $row['content'] = json_decode($row['content'], true);
            $row['meta'] = [
                'name' => $row['name'],
                'category' => $row['category']
            ];

            unset($row['name'], $row['category']);

            return $row;
        } else {
            return false;
        }
    }

    /**
     * Delete history items by their associated ids
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids)
    {
        foreach ($ids as $id) {
            if (!$this->historyMapper->deleteById($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Filters the raw input
     * 
     * @param array|\ArrayAccess $input Raw input data
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page to be displayed
     * @param string $sortingColumn Column name to be sorted
     * @param string $desc Whether to sort in DESC order
     * @param array $params Extra parameters
     * @return array
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc, array $params = array())
    {
        return $this->prepareResults($this->historyMapper->filter($input, $page, $itemsPerPage, $sortingColumn, $desc));
    }

    /**
     * Returns all records
     * 
     * @param integer $page
     * @param integer $itemsPerPage
     * @return array
     */
    public function fetchAll($page, $itemsPerPage)
    {
        return $this->prepareResults($this->historyMapper->fetchAll($page, $itemsPerPage));
    }

    /**
     * Returns prepared paginator instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->historyMapper->getPaginator();
    }

    /**
     * Tracks the history
     * 
     * @param array $data
     * @return boolean
     */
    public function track(array $data)
    {
        $data['timestamp'] = time();
        $data['slug'] = uniqid();

        return $this->historyMapper->persistRow($data);
    }
}
