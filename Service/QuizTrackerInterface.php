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
     * Appends correct question id
     * 
     * @param string $questionId
     * @param string $answerId
     * @return void
     */
    public function appendCorrectQuestionId($questionId);

    /**
     * Returns an amount of correctly answered questions
     * 
     * @return integer
     */
    public function getCorrectAnsweredCount();

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
