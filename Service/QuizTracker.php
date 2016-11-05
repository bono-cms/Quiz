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
use Krystal\Session\SessionBagInterface;

final class QuizTracker extends AbstractManager implements QuizTrackerInterface
{
    /**
     * Session bag service
     * 
     * @var \Krystal\Session\SessionBagInterface
     */
    private $sessionBag;

    /**
     * Category identificators
     * 
     * @var array
     */
    private $identificators = array();

    const PARAM_STORAGE_KEY = 'quiz_track';
    const PARAM_STORAGE_INITIAL_COUNT = 'quiz_track_initial_count';
    const PARAM_STORAGE_TIMESTAMP_START = 'quiz_timestamp_start';
    const PARAM_STORAGE_META_DATA = 'quiz_meta';
    const PARAM_STORAGE_CORRECT_IDS = 'quiz_correct_ids';

    /**
     * State initialization
     * 
     * @param \Krystal\Session\SessionBagInterface $sessionBag
     * @return void
     */
    public function __construct(SessionBagInterface $sessionBag)
    {
        $this->sessionBag = $sessionBag;
    }

    /**
     * Checks whether tracking is started
     * 
     * @return boolean
     */
    public function isStarted()
    {
        return $this->sessionBag->get(self::PARAM_STORAGE_KEY) !== false;
    }

    /**
     * Start tracking
     * 
     * @param array $identificators
     * @return void
     */
    public function start(array $identificators)
    {
        $this->sessionBag->setMany(array(
            self::PARAM_STORAGE_KEY => $identificators,
            self::PARAM_STORAGE_INITIAL_COUNT => count($identificators),
            self::PARAM_STORAGE_TIMESTAMP_START => time()
        ));

        return $this;
    }

    /**
     * Appends correct question id
     * 
     * @param string $questionId
     * @param string $answerId
     * @return void
     */
    public function appendCorrectQuestionId($questionId)
    {
        // Lazy initialization
        if (!$this->sessionBag->has(self::PARAM_STORAGE_CORRECT_IDS)) {
            $this->sessionBag->set(self::PARAM_STORAGE_CORRECT_IDS, array());
        }

        // Get the currect collection
        $collection = $this->sessionBag->get(self::PARAM_STORAGE_CORRECT_IDS);

        // Append a new item
        $collection[] = $questionId;

        // Update the storage with altered collection
        $this->sessionBag->set(self::PARAM_STORAGE_CORRECT_IDS, $collection);
    }

    /**
     * Returns an amount of correctly answered questions
     * 
     * @return integer
     */
    public function getCorrectAnsweredCount()
    {
        $collection = $this->sessionBag->get(self::PARAM_STORAGE_CORRECT_IDS);

        if (is_array($collection)) {
            return count($collection);
        } else {
            return 0;
        }
    }

    /**
     * Saves meta data
     * 
     * @param array $data Data to be saved
     * @return void
     */
    public function saveMeta(array $data)
    {
        $this->sessionBag->set(self::PARAM_STORAGE_META_DATA, $data);
    }

    /**
     * Returns saved meta data if available
     * 
     * @return array
     */
    public function getMeta()
    {
        return $this->sessionBag->get(self::PARAM_STORAGE_META_DATA);
    }

    /**
     * Returns taken time
     * 
     * @return string
     */
    public function getTakenTime()
    {
        return time() - $this->sessionBag->get(self::PARAM_STORAGE_TIMESTAMP_START);
    }

    /**
     * Returns initial count
     * 
     * @return integer
     */
    public function getInitialCount()
    {
        return $this->sessionBag->get(self::PARAM_STORAGE_INITIAL_COUNT);
    }

    /**
     * Returns current question count
     * 
     * @return integer
     */
    public function getCurrentQuestionCount()
    {
        $collection = $this->sessionBag->get(self::PARAM_STORAGE_KEY);
        return $this->getInitialCount() - count($collection);
    }

    /**
     * Creates question id
     * 
     * @return string|boolean
     */
    public function createQuestionId()
    {
        $collection = $this->sessionBag->get(self::PARAM_STORAGE_KEY);

        // The collection is empty, the immediatelly stop returning false
        if (empty($collection)) {
            return false;
        } else {
            // Save first item in collection and them immediatelly remove it
            $id = array_shift($collection);

            // Update the collection
            $this->sessionBag->set(self::PARAM_STORAGE_KEY, $collection);

            return $id;
        }
    }
}
