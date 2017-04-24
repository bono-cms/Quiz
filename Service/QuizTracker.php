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
use Krystal\Date\TimeHelper;
use LogicException;

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
    const PARAM_STORAGE_STOPPED = 'quiz_stopped';

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
     * Clears the storage
     * 
     * @return void
     */
    public function clear()
    {
        return $this->sessionBag->removeMany(array(
            self::PARAM_STORAGE_KEY,
            self::PARAM_STORAGE_INITIAL_COUNT,
            self::PARAM_STORAGE_TIMESTAMP_START,
            self::PARAM_STORAGE_META_DATA,
            self::PARAM_STORAGE_CORRECT_IDS,
            self::PARAM_STORAGE_STOPPED
        ));
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
     * Indicates that the quiz must be stopped
     * 
     * @return void
     */
    public function stop()
    {
        $this->sessionBag->set(self::PARAM_STORAGE_STOPPED, true);
    }

    /**
     * Checks whether stopping has been indicated before
     * 
     * @return boolean
     */
    public function isStopped()
    {
        return $this->sessionBag->has(self::PARAM_STORAGE_STOPPED);
    }

    /**
     * Returns points
     * 
     * @param integer $mark Whether to return as a mark or not
     * @return integer
     */
    public function getPoints($mark)
    {
        if ($mark === true) {
            return $this->createMark();
        } else {
            return $this->getCorrectAnsweredCount();
        }
    }

    /**
     * Creates a mark
     * 
     * @return integer
     */
    private function createMark()
    {
        // Counter of correct answers
        $correctCount = $this->getCorrectAnsweredCount();
        $totalCount = $this->getInitialCount();

        // If no correct answers provided, then the mark is always zero
        if ($correctCount == 0 || $totalCount == 0) {
            return 0;
        }

        return (int) $correctCount * 100 / $totalCount;
    }

    /**
     * Returns an amount of correctly answered questions
     * 
     * @return integer
     */
    private function getCorrectAnsweredCount()
    {
        $collection = $this->sessionBag->get(self::PARAM_STORAGE_CORRECT_IDS);

        if (is_array($collection)) {
            return count($collection);
        } else {
            return 0;
        }
    }

    /**
     * Appends correct question id
     * 
     * @param string $questionId
     * @return void
     */
    public function appendCorrectQuestionId($questionId)
    {
        // Lazy initialization
        if (!$this->sessionBag->has(self::PARAM_STORAGE_CORRECT_IDS)) {
            $this->sessionBag->set(self::PARAM_STORAGE_CORRECT_IDS, array());
        }

        // Get the current collection
        $collection = $this->sessionBag->get(self::PARAM_STORAGE_CORRECT_IDS);

        // Append a new item
        $collection[] = $questionId;

        // Update the storage with altered collection
        $this->sessionBag->set(self::PARAM_STORAGE_CORRECT_IDS, $collection);
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
     * @throws \LogicException if tried to get taken time when quiz isn't finished
     * @return string
     */
    public function getTakenTime()
    {
        if ($this->isStopped()) {
            return TimeHelper::getTakenTime($this->sessionBag->get(self::PARAM_STORAGE_TIMESTAMP_START), time());
        } else {
            throw new LogicException('Can not get taken time if the quiz is not finished');
        }
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

        // The collection is empty, the immediately stop returning false
        if (empty($collection)) {
            return false;
        } else {
            // Save first item in collection and them immediately remove it
            $id = array_shift($collection);

            // Update the collection
            $this->sessionBag->set(self::PARAM_STORAGE_KEY, $collection);

            return $id;
        }
    }
}
