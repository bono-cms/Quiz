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
use Quiz\Storage\CategoryMapperInterface;
use Quiz\Storage\QuestionMapperInterface;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;

final class CategoryService extends AbstractManager implements CategoryServiceInterface
{
    /**
     * Any compliant category mapper
     * 
     * @var \Quiz\Storage\CategoryMapperInterface
     */
    private $categoryMapper;

    /**
     * Any compliant answer mapper
     * 
     * @var \Quiz\Storage\QuestionMapperInterface
     */
    private $questionMapper;

    /**
     * State initialization
     * 
     * @param \Quiz\Storage\CategoryMapperInterface $categoryMapper
     * @param \Quiz\Storage\QuestionMapperInterface $questionMapper
     * @return void
     */
    public function __construct(CategoryMapperInterface $categoryMapper, QuestionMapperInterface $questionMapper)
    {
        $this->categoryMapper = $categoryMapper;
        $this->questionMapper = $questionMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'], VirtualEntity::FILTER_INT)
               ->setName($row['name'], VirtualEntity::FILTER_TAGS)
               ->setOrder($row['order'], VirtualEntity::FILTER_INT)
               ->setQuestionsCount($this->questionMapper->countAllByCategoryId($row['id']), VirtualEntity::FILTER_INT);

        return $entity;
    }

    /**
     * Fetches category list
     * 
     * @param boolean $values Whether to fetch only values
     * @return array
     */
    public function fetchList($values = false)
    {
        $rows = ArrayUtils::arrayList($this->categoryMapper->fetchAll(true), 'id', 'name');

        if ($values == true) {
            $rows = ArrayUtils::valuefy($rows);
        }

        return $rows;
    }

    /**
     * Fetch all categories
     * 
     * @param boolean $sort Whether to use sorting by order attribute or not
     * @return array
     */
    public function fetchAll($sort)
    {
        return $this->prepareResults($this->categoryMapper->fetchAll($sort));
    }

    /**
     * Deletes a category by its associated id
     * 
     * @param string $id Category id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->categoryMapper->deleteById($id) && $this->questionMapper->deleteAllByCategoryId($id);
    }

    /**
     * Returns category entity or false on failure
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->categoryMapper->fetchById($id));
    }

    /**
     * Fetch category name by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id)
    {
        return $this->categoryMapper->fetchNameById($id);
    }

    /**
     * Returns last category id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->categoryMapper->getLastId();
    }

    /**
     * Adds a category
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        $input['order'] = (int) $input['order'];
        return $this->categoryMapper->insert($input);
    }

    /**
     * Updates a category
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        $input['order'] = (int) $input['order'];
        return $this->categoryMapper->update($input);
    }
}
