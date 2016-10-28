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

use Cms\Service\AbstractManager;
use Quiz\Storage\QuestionMapperInterface;
use Krystal\Stdlib\VirtualEntity;

final class QuestionService extends AbstractManager implements QuestionServiceInterface
{
    /**
     * Any compliant answer mapper
     * 
     * @var \Quiz\Storage\QuestionMapperInterface
     */
    private $questionMapper;

    /**
     * State initialization
     * 
     * @param \Quiz\Storage\QuestionMapperInterface $questionMapper
     * @return void
     */
    public function __construct(QuestionMapperInterface $questionMapper)
    {
        $this->questionMapper = $questionMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $question)
    {
        $entity = new VirtualEntity();
        $entity->setId($question['id'], VirtualEntity::FILTER_INT)
               ->setCategoryId($question['category_id'], VirtualEntity::FILTER_INT)
               ->setQuestion($question['question'], VirtualEntity::FILTER_HTML)
               ->setOrder($question['order'], VirtualEntity::FILTER_INT);

        return $entity;
    }

    /**
     * Update orders by their associated ids
     * 
     * @param array $pairs
     * @return boolean
     */
    public function updateOrders(array $pairs)
    {
        foreach ($pairs as $id => $order) {
            if (!$this->questionMapper->updateOrderById($id, $order)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns prepared pagination instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->questionMapper->getPaginator();
    }

    /**
     * Fetches all questions entities associated with category id
     * 
     * @param string $id Category id
     * @param integer $page Current page number
     * @param integer $itemPerPage Per page count
     * @return array
     */
    public function fetchAllByCategoryId($id, $page, $itemsPerPage)
    {
        return $this->prepareResults($this->questionMapper->fetchAllByCategoryId($id, $page, $itemsPerPage));
    }

    /**
     * Fetches question entity by its associate id
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->questionMapper->fetchById($id));
    }

    /**
     * Deletes a question by its associate di
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->questionMapper->deleteById($id);
    }

    /**
     * Returns last question id
     * 
     * @return string
     */
    public function getLastId()
    {
        return $this->questionMapper->getLastId();
    }

    /**
     * Adds a question
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        $data = $input['question'];
        return $this->questionMapper->insert($data);
    }

    /**
     * Updates a question
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        $data = $input['question'];
        return $this->questionMapper->update($data);
    }
}
