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

final class QuizTracker extends AbstractManager
{
    /**
     * Session bag service
     * 
     * @var \Krystal\Session\SessionBagInterface
     */
    private $sessionBag;

    const PARAM_STORAGE_CATEGORY_IDS = 'quiz_category_ids';
    const PARAM_STORAGE_PASSED = 'quiz_passed';
    const PARAM_STORAGE_CURRENT_COUNT = 'quiz_current_count';
    const PARAM_STORAGE_INITIAL_COUNT = 'quiz_track_initial_count';
    const PARAM_STORAGE_TIMESTAMP_START = 'quiz_timestamp_start';
    const PARAM_STORAGE_TIMESTAMP_END = 'quiz_timestamp_end';
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
     * Set initial state
     * 
     * @param array $ids
     * @return void
     */
    public function setCategoryIds(array $ids)
    {
        $this->sessionBag->set(self::PARAM_STORAGE_CATEGORY_IDS, $ids);
    }

    /**
     * Excludes category id
     * 
     * @param mixed $id
     * @return boolean
     */
    public function excludeCategoryId($id)
    {
        $data = $this->sessionBag->get(self::PARAM_STORAGE_CATEGORY_IDS);
        $output = [];

        // Find and exclude current id
        foreach ($data as $value) {
            if ($id != $value) {
                $output[] = $value;
            }
        }

        return $this->setCategoryIds($output);
    }

    /**
     * Returns next category id
     * 
     * @return mixed
     */
    public function getNextCategoryId()
    {
        $data = $this->sessionBag->get(self::PARAM_STORAGE_CATEGORY_IDS);

        if (!empty($data)) {
            return $data[0];
        } else {
            return null;
        }
    }

    /**
     * Clears the storage
     * 
     * @return void
     */
    public function clear()
    {
        return $this->sessionBag->removeMany(array(
            self::PARAM_STORAGE_CATEGORY_IDS,
            self::PARAM_STORAGE_PASSED,
            self::PARAM_STORAGE_CURRENT_COUNT,
            self::PARAM_STORAGE_INITIAL_COUNT,
            self::PARAM_STORAGE_TIMESTAMP_START,
            self::PARAM_STORAGE_TIMESTAMP_END,
            self::PARAM_STORAGE_META_DATA,
            self::PARAM_STORAGE_CORRECT_IDS,
            self::PARAM_STORAGE_STOPPED
        ));
    }

    /**
     * Returns previous tracking number
     * 
     * @return int
     */
    public function getPrevCount()
    {
        $count = $this->getCurrentCount();

        if ($count > 1) {
            $count--;
        } else {
            $count = 1;
        }

        $this->sessionBag->set(self::PARAM_STORAGE_CURRENT_COUNT, $count);
        return $count;
    }

    /**
     * Whether current tracking number is first
     * 
     * @return boolean
     */
    public function isFirstQuestion()
    {
        return $this->getCurrentCount() == 1;
    }

    /**
     * Whether current tracking number is last
     * 
     * @return boolean
     */
    public function isLastCount()
    {
        return $this->getInitialCount() == $this->getCurrentCount();
    }

    /**
     * Returns next tracking number
     * 
     * @return int
     */
    public function getNextCount()
    {
        $count = $this->getCurrentCount();
        $count++;

        $this->sessionBag->set(self::PARAM_STORAGE_CURRENT_COUNT, $count);
        return $count;
    }

    /**
     * Returns current count
     * 
     * @return int
     */
    public function getCurrentCount()
    {
        // If not started yet, start by 1, by default
        if (!$this->sessionBag->has(self::PARAM_STORAGE_CURRENT_COUNT)) {
            $this->sessionBag->set(self::PARAM_STORAGE_CURRENT_COUNT, 0);
        }

        return $this->sessionBag->get(self::PARAM_STORAGE_CURRENT_COUNT);
    }

    /**
     * Checks whether tracking is started
     * 
     * @return boolean
     */
    public function isStarted()
    {
        return $this->sessionBag->get(self::PARAM_STORAGE_TIMESTAMP_START) !== false;
    }

    /**
     * Start tracking
     * 
     * @param int $count
     * @return void
     */
    public function start($count)
    {
        $this->sessionBag->setMany(array(
            self::PARAM_STORAGE_INITIAL_COUNT => $count,
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
        $this->sessionBag->set(self::PARAM_STORAGE_TIMESTAMP_END, time());

        // Remove tracking count
        if ($this->sessionBag->has(self::PARAM_STORAGE_CURRENT_COUNT)) {
            $this->sessionBag->remove(self::PARAM_STORAGE_CURRENT_COUNT);
        }
    }

    /**
     * Checks whether stopping has been indicated before
     * 
     * @return boolean
     */
    public function isStopped()
    {
        return $this->sessionBag->has(self::PARAM_STORAGE_TIMESTAMP_END);
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
            $collection = array_unique($collection);
            return count($collection);
        } else {
            return 0;
        }
    }

    /**
     * Returns last passed question id
     * 
     * @return mixed
     */
    public function getLastPassed()
    {
        $passed = $this->getPassed(false);

        if (!empty($passed)) {
            return end($passed);
        } else {
            return null;
        }
    }

    /**
     * Returns a collection of passed questions with their ansnwers
     * 
     * @param boolean $complete Whether to return only question ids with or without answer ids
     * @return array
     */
    public function getPassed($complete)
    {
        // Lazy initialization
        if (!$this->sessionBag->has(self::PARAM_STORAGE_PASSED)) {
            $this->sessionBag->set(self::PARAM_STORAGE_PASSED, array());
        }

        $passed = $this->sessionBag->get(self::PARAM_STORAGE_PASSED);

        if ($complete) {
            return $passed;
        } else {
            return array_keys($passed);
        }
    }

    /**
     * Append passed question and their answer ids
     * 
     * @param int $questionId
     * @param array $answerIds
     * @return void
     */
    public function appendPassed($questionId, array $answerIds)
    {
        $passed = $this->getPassed(true);

        // $questionId is unique, so that it can not be repeated
        $passed[$questionId] = $answerIds;

        // Update the storage with altered collection
        $this->sessionBag->set(self::PARAM_STORAGE_PASSED, $passed);
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
            return TimeHelper::getTakenTime(
                $this->sessionBag->get(self::PARAM_STORAGE_TIMESTAMP_START), 
                $this->sessionBag->get(self::PARAM_STORAGE_TIMESTAMP_END)
            );
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
}
