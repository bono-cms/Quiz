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
use Quiz\Storage\AnswerMapperInterface;
use Krystal\Stdlib\VirtualEntity;

final class AnswerService extends AbstractManager implements AnswerServiceInterface
{
    /**
     * Any compliant answer mapper
     * 
     * @var \Quiz\Storage\AnswerMapperInterface
     */
    private $answerMapper;

    /**
     * State initialization
     * 
     * @param \Quiz\Storage\AnswerMapperInterface $answerMapper
     * @return void 
     */
    public function __construct(AnswerMapperInterface $answerMapper)
    {
        $this->answerMapper = $answerMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'], VirtualEntity::FILTER_INT)
               ->setQuestionId($row['question_id'])
               ->setAnswer($row['answer'], VirtualEntity::FILTER_TAGS)
               ->setOrder($row['order'], VirtualEntity::FILTER_INT)
               ->setCorrect($row['correct']);

        return $entity;
    }

    /**
     * Returns last answer id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->answerMapper->getLastId();
    }

    /**
     * Adds an answer
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input)
    {
        $data = $input['answer'];
        return $this->answerMapper->insert($data);
    }

    /**
     * Updates an answer
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input)
    {
        $data = $input['answer'];
        return $this->answerMapper->update($data);
    }

    /**
     * Deletes an answer by its id
     * 
     * @param string $id Answer id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->answerMapper->deleteById($id);
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
        return $this->prepareResults($this->answerMapper->fetchAll($id, $sort));
    }

    /**
     * Fetches an answer by its id
     * 
     * @param string $id Answer id
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->answerMapper->fetchById($id));
    }
}
