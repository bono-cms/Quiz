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

final class AnswerService extends AbstractManager
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
               ->setCorrect($row['correct'])
               ->setSignature(uniqid());

        return $entity;
    }

    /**
     * Checks whether answer is correct
     * 
     * @param string $questionId
     * @param string $answerId
     * @return boolean
     */
    public function isCorrect($questionId, $answerId)
    {
        return $this->answerMapper->getCorrect($questionId, $answerId) == '1';
    }

    /**
     * Checks whether there only one or several correct answers
     * 
     * @param array $entities
     * @return boolean
     */
    public function hasManyCorrectAnswers(array $entities)
    {
        $count = 0;

        foreach ($entities as $entity) {
            // If the answer is correct
            if ($entity->getCorrect()) {
                $count++;
            }
        }

        return $count > 1;
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
        $data['order'] = (int) $data['order'];

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
        $data['order'] = (int) $data['order'];

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
