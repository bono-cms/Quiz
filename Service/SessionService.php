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

use Krystal\Stdlib\ArrayUtils;
use Krystal\Session\SessionBagInterface;
use Quiz\Storage\SessionTrackMapperInterface;
use Quiz\Storage\SessionMapperInterface;

final class SessionService
{
    const PARAM_STORAGE_SESSION_ID = 'quiz__session_id';
    const PARAM_STORAGE_SESSION_LAST_QUIZ_ID = 'quiz__session_last_id';

    /**
     * Session mapper
     * 
     * @var \Quiz\Storage\SessionMapperInterface
     */
    private $sessionMapper;

    /**
     * Session track mapper
     * 
     * @var \Quiz\Storage\SessionTrackMapperInterface
     */
    private $sessionTrackMapper;

    /**
     * Session bag service
     * 
     * @var \Krystal\Session\SessionBagInterface
     */
    private $sessionBag;

    /**
     * State initialization
     * 
     * @param \Quiz\Storage\SessionMapperInterface $sessionMapper
     * @param \Quiz\Storage\SessionTrackMapperInterface $sessionTrackMapper
     * @param \Krystal\Session\SessionBagInterface $sessionBag
     * @return void
     */
    public function __construct(
        SessionMapperInterface $sessionMapper,
        SessionTrackMapperInterface $sessionTrackMapper,
        SessionBagInterface $sessionBag
    ){
        $this->sessionMapper = $sessionMapper;
        $this->sessionTrackMapper = $sessionTrackMapper;
        $this->sessionBag = $sessionBag;
    }

    /**
     * Checks whether session is started
     * 
     * @return boolean
     */
    public function isStarted()
    {
        return $this->sessionBag->has(self::PARAM_STORAGE_SESSION_ID);
    }

    /**
     * Starts a session
     * 
     * @return boolean
     */
    public function start()
    {
        $this->sessionMapper->persist([
            'started' => time()
        ]);

        $id = $this->sessionMapper->getMaxId();
        $this->sessionBag->set(self::PARAM_STORAGE_SESSION_ID, $id);

        return true;
    }

    /**
     * Finish a session
     * 
     * @return boolean
     */
    public function finish()
    {
        // Can only finish started session
        if ($this->isStarted()) {
            $this->sessionMapper->persist([
                'id' => $this->sessionBag->get(self::PARAM_STORAGE_SESSION_ID),
                'finished' => time()
            ]);

            $this->sessionBag->remove(self::PARAM_STORAGE_SESSION_ID);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns session id
     * 
     * @return mixed
     */
    public function getId()
    {
        return $this->sessionBag->get(self::PARAM_STORAGE_SESSION_ID);
    }

    /**
     * Fetch all items
     * 
     * @param int $sessionId
     * @return array
     */
    public function fetchAll($sessionId)
    {
        $rows = $this->sessionTrackMapper->fetchAll($sessionId);

        foreach ($rows as &$row) {
            $row['answers'] = json_decode($row['answers'], true);
        }

        return ArrayUtils::arrayPartition($rows, 'category', false);
    }

    /**
     * Parse answers
     * 
     * @param array $answers A collection of answers
     * @param array $selectedIds
     * @return string
     */
    private function parseAnswers(array $answers, array $selectedIds)
    {
        $items = [];

        foreach ($answers as $answer) {
            $items[] = [
                'id' => (int) $answer['id'],
                'correct' => (bool) $answer['correct'],
                'answer' => $answer['answer'],
                'selected' => in_array($answer['id'], $selectedIds)
            ];
        }

        return $items;
    }

    /**
     * Tracks a rendered question
     * 
     * @param string $category Category name
     * @param string $question Question title
     * @return void
     */
    public function trackRender($category, $question)
    {
        // Can only track started session
        if ($this->isStarted()) {
            $row = $this->sessionTrackMapper->persistRow([
                'session_id' => $this->sessionBag->get(self::PARAM_STORAGE_SESSION_ID),
                'category' => $category,
                'question' => $question
            ]);

            $this->sessionBag->set(self::PARAM_STORAGE_SESSION_LAST_QUIZ_ID, $row['id']);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Tracks a response
     * 
     * @param array $answers A collection of answers
     * @param array $selectedIds
     * @return boolean Depending on success
     */
    public function trackResponse(array $answers, array $selectedIds)
    {
        // Can only track started session
        if ($this->isStarted()) {
            $sessionId = $this->sessionBag->get(self::PARAM_STORAGE_SESSION_LAST_QUIZ_ID);

            $answers = json_encode($this->parseAnswers($answers, $selectedIds), \JSON_UNESCAPED_UNICODE);
            $this->sessionTrackMapper->updateTrack($sessionId, $answers);

            return true;
        }

        return false;
    }
}
