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

interface QuizTrackerInterface
{
    /**
     * Clears the storage
     * 
     * @return void
     */
    public function clear();

    /**
     * Checks whether tracking is started
     * 
     * @return boolean
     */
    public function isStarted();
    
    /**
     * Start tracking
     * 
     * @param array $identificators
     * @return void
     */
    public function start(array $identificators);

    /**
     * Indicates that the quiz must be stopped
     * 
     * @return void
     */
    public function stop();

    /**
     * Checks whether stopping has been indicated before
     * 
     * @return boolean
     */
    public function isStopped();

    /**
     * Returns points
     * 
     * @param integer $mark Whether to return as a mark or not
     * @return integer
     */
    public function getPoints($mark);

    /**
     * Appends correct question id
     * 
     * @param string $questionId
     * @param string $answerId
     * @return void
     */
    public function appendCorrectQuestionId($questionId);

    /**
     * Saves meta data
     * 
     * @param array $data Data to be saved
     * @return void
     */
    public function saveMeta(array $data);

    /**
     * Returns saved meta data if available
     * 
     * @return array
     */
    public function getMeta();

    /**
     * Returns taken time
     * 
     * @throws \LogicException if tried to get taken time when quiz isn't finished
     * @return string
     */
    public function getTakenTime();

    /**
     * Returns initial count
     * 
     * @return integer
     */
    public function getInitialCount();

    /**
     * Returns current question count
     * 
     * @return integer
     */
    public function getCurrentQuestionCount();

    /**
     * Creates question id
     * 
     * @return string|boolean
     */
    public function createQuestionId();
}
