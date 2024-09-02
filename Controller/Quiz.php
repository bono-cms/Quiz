<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Controller;

use Site\Controller\AbstractController;
use Krystal\Validate\Pattern;
use Krystal\Stdlib\VirtualEntity;

final class Quiz extends AbstractController
{
    /* View templates */
    const QUIZ_TEMPLATE_QUIZ = 'quiz';
    const QUIZ_TEMPLATE_RESULT = 'result';
    const QUIZ_TEMPLATE_WELCOME = 'welcome';
    const QUIZ_TEMPLATE_EMPTY_CAT = 'empty';

    /**
     * {@inheritDoc}
     */
    protected function bootstrap($action)
    {
        parent::bootstrap($action);

        $this->loadSitePlugins();

        // Configure view
        $this->view->setLayout('__layout__')
                   ->setModule('Quiz')
                   ->setTheme('site');

        $this->view->addVariables(array(
            'languages' => $this->getService('Pages', 'pageManager')->getSwitchUrls(null)
        ));
    }

    /**
     * Runs the initial test
     * 
     * @return string
     */
    public function indexAction()
    {
        $quizTracker = $this->getModuleService('quizTracker');
        $page = $this->createEntity();

        // Do pre-processing if not started yet
        if (!$quizTracker->isStarted()) {
            $welcome = $this->welcomeAction($page);

            if ($welcome !== true) {
                return $welcome;
            }
        } else {
            // Answer page
            if ($this->request->isPost()) {
                $answer = $this->answerAction();
                if ($answer !== true) {
                    return $answer;
                }
            } else {
                // @TODO Do nothing or render the same question
            }
        }

        $id = $this->getQuestionId($quizTracker->getCurrentCategoryId());

        // If $id is false, then there's no more questions to be shown
        // Or if the provided limit exceeds the current track count
        if (!$id || ($this->getLimit() !== null && $this->getLimit() < $quizTracker->getCurrentCount())) {
            return $this->stopAction($page);
        }

        return $this->quizAction($page, $id);
    }

    /**
     * Continues test by next category id
     * 
     * @return string
     */
    public function continueAction()
    {
        if (!$this->canContinue()) {
            return false;
        }

        // Get services
        $quizTracker = $this->getModuleService('quizTracker');
        $questionService = $this->getModuleService('questionService');

        $categoryId = $quizTracker->getNextCategoryId();

        // Continue, if found a category
        if ($categoryId !== null) {
            $count = $questionService->countQuestionsByCategoryId($categoryId, $this->getLimit());

            $quizTracker->setCurrentCategoryId($categoryId);
            $quizTracker->start($count);
            $quizTracker->resetCount(); // Reset trucking number count

            $id = $this->getQuestionId($quizTracker->getCurrentCategoryId());
            return $this->quizAction($this->createEntity(), $id);

        } else {
            // Can not continue. No more categories left.
            return ('No more categories left');
        }
    }

    /**
     * Aborts the quiz
     * 
     * @return void
     */
    public function abortAction()
    {
        $quizTracker = $this->getModuleService('quizTracker');
        $quizTracker->clear();

        return $this->redirectToRoute('Quiz:Quiz@indexAction');
    }

    /**
     * Outputs and handlers welcome page
     * 
     * @param \Krystal\Stdlib\VirtualEntity $page
     * @return mixed
     */
    private function welcomeAction(VirtualEntity $page)
    {
        $quizTracker = $this->getModuleService('quizTracker');

        // If the welcoming form was submitted, then grab and save its value and start tracking
        if ($this->request->hasPost('category')) {
            $formValidator = $this->createValidator(array(
                'input' => array(
                    'source' => $this->request->getPost(),
                    'definition' => array(
                        'name' => new Pattern\Name()
                    )
                )
            ));

            if ($formValidator->isValid()) {
                $questionService = $this->getModuleService('questionService');
                // Initial loading from request
                $categoryId = $this->request->getPost('category');

                // Get total count
                $count = $questionService->countQuestionsByCategoryId($categoryId, $this->getLimit());

                // Does this category even have quesions?
                if ($count == 0) {
                    return $this->view->render(self::QUIZ_TEMPLATE_EMPTY_CAT, array(
                        'page' => $page
                    ));
                }

                // Save category ids initially
                $quizTracker->setCategoryIds($this->getModuleService('categoryService')->fetchNonEmptyCategoryIds());
                $quizTracker->setCurrentCategoryId($categoryId);
                $quizTracker->start($count);
                $quizTracker->saveMeta(array(
                    'name' => $this->request->getPost('name'),
                    'category' => $this->getModuleService('categoryService')->fetchNameById($categoryId)
                ));

                return true;
            } else {
                return $formValidator->getErrors();
            }

        } else {
            // In case that was the first GET request, render welcome page
            return $this->view->render(self::QUIZ_TEMPLATE_WELCOME, array(
                'categories' => $this->getModuleService('categoryService')->fetchList(),
                'page' => $page
            ));
        }
    }

    /**
     * Stops the quiz
     * 
     * @param \Krystal\Stdlib\VirtualEntity $page
     * @return string
     */
    private function stopAction(VirtualEntity $page)
    {
        $quizTracker = $this->getModuleService('quizTracker');
        $points = $quizTracker->getPoints(true);

        // Do save track only in case the stopping has been indicated
        if (!$quizTracker->isStopped()) {
            // Keep the track
            $this->getModuleService('historyService')->track(array_merge($quizTracker->getMeta(), array(
                'timestamp' => time(),
                'points' => $points
            )));
        }

        // First priority
        $quizTracker->excludeCategoryId($quizTracker->getCurrentCategoryId());

        // Whether can continue
        $canContinue = $this->canContinue();

        // Indicate stopping, if can't go on
        if (!$canContinue) {
            $quizTracker->stop();
        }

        return $this->view->render(self::QUIZ_TEMPLATE_RESULT, array(
            'meta' => $quizTracker->getMeta(),
            'takenTime' => $quizTracker->getTakenTime(),
            'points' => $points,
            'page' => $page,
            'canContinue' => $canContinue
        ));
    }

    /**
     * Answers to particular question id
     * 
     * @return mixed
     */
    private function answerAction()
    {
        $quizTracker = $this->getModuleService('quizTracker');
        $questionId = $this->request->getPost('question');

        // Answer ids
        $answerIds = $this->request->getPost('answerIds', array());
        $input['collection'] = $answerIds;

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'collection' => new Pattern\Collection()
                )
            )
        ));

        // Make sure that at least one answer is picked
        if ($formValidator->isValid()) {
            // Append passed question ID with its answers choices
            // @TODO: This should be tracked only for random items
            $quizTracker->appendPassed($questionId, $answerIds);

            // Keep track of correctness
            foreach ($answerIds as $answerId) {
                $correct = $this->getModuleService('answerService')->isCorrect($questionId, $answerId);

                if ($correct) {
                    $quizTracker->appendCorrectQuestionId($questionId);
                }
            }

            return true;
        } else {
            return $formValidator->getErrors();
        }
    }

    /**
     * Renders quiz page
     * 
     * @param \Krystal\Stdlib\VirtualEntity $page
     * @param string $id Question id
     * @return string
     */
    private function quizAction(VirtualEntity $page, $id)
    {
        $quizTracker = $this->getModuleService('quizTracker');
        $data = $this->createPair($id);

        return $this->view->render(self::QUIZ_TEMPLATE_QUIZ, array_merge($data, array(
            'page' => $page,
            'hasManyCorrectAnswers' => $this->getModuleService('answerService')->hasManyCorrectAnswers($data['answers']),
            'initialCount' => $quizTracker->getInitialCount(),
            'currentQuestionCount' => $quizTracker->getCurrentCount(),
            'lastQuestion' => $quizTracker->isLastCount(),
            'firstQuestion' => $quizTracker->isFirstQuestion()
        )));
    }

    /**
     * Creates a pair
     * 
     * @param string $id
     * @return array
     */
    private function createPair($id)
    {
        $question = $this->getModuleService('questionService')->fetchById($id);
        $answers = $this->getModuleService('answerService')->fetchAll($id, true);

        return array(
            'question' => $question,
            'answers' => $answers
        );
    }

    /**
     * Returns limit for questions stack, if provided
     * 
     * @return mixed
     */
    private function getLimit()
    {
        $limit = $this->getModuleService('configManager')
                      ->getEntity()
                      ->getLimit();

        if (is_numeric($limit) && $limit > 0) {
            return $limit;
        } else {
            return null;
        }
    }

    /**
     * Generates current question id depending on position
     * 
     * @param int $categoryId Category id
     * @return int|mixed
     */
    private function getQuestionId($categoryId)
    {
        $questionService = $this->getModuleService('questionService');
        $quizTracker = $this->getModuleService('quizTracker');
        $config = $this->getModuleService('configManager')->getEntity();

        // Logic for non-random
        if ($config->sortByOrder()) {
            if ($this->request->hasQuery('prev')) {
                $trackNumber = $quizTracker->getPrevCount();
            } else {
                $trackNumber = $quizTracker->getNextCount();
            }

            $id = $questionService->fetchQuiestionIdByCategoryId(
                $categoryId,
                $config->getSortingMethod(),
                $trackNumber
            );
        } else {
            // Logic for random
            if ($this->request->hasQuery('prev')) {
                $quizTracker->getPrevCount();
                $id = $quizTracker->getLastPassed();
            } else {
                // Keep the track
                $quizTracker->getNextCount();
                $id = $questionService->fetchRandomQuestionIdByCategoryId($categoryId, $quizTracker->getPassed(false));
            }
        }

        return $id;
    }

    /**
     * Creates page entity
     * 
     * @return \Krystal\Stdlib\VirtualEntity
     */
    private function createEntity()
    {
        $page = new VirtualEntity();
        $page->setSeo(false)
             ->setTitle($this->translator->translate('Passing the quiz'));

        return $page;
    }

    /**
     * Checks whether user can continue (allowed) with different category
     * 
     * @return boolean
     */
    private function canContinue()
    {
        $config = $this->getModuleService('configManager')->getEntity();
        $quizTracker = $this->getModuleService('quizTracker');

        return !$config->shouldStop() && $quizTracker->hasNextCategoryId();
    }
}
