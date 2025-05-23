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
    const QUIZ_TEMPLATE_QUIZ = 'quiz-main';
    const QUIZ_TEMPLATE_RESULT = 'quiz-result';
    const QUIZ_TEMPLATE_WELCOME = 'quiz-welcome';
    const QUIZ_TEMPLATE_EMPTY_CAT = 'quiz-empty';
    const QUIZ_TEMPLATE_SESSION = 'quiz-session';

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
     * Find history item by its slug
     * 
     * @param string $slug
     * @return mixed
     */
    public function historyAction($slug)
    {
        $item = $this->getModuleService('historyService')->fetchBySlug($slug);

        if ($item) {
            return $this->view->render(self::QUIZ_TEMPLATE_RESULT, array(
                'meta' => $item['meta'],
                'points' => $item['points'],
                'page' => $this->createEntity(),
                'canContinue' => false,
                'scores' => $item['content'],
                'url' => $this->request->getBaseUrl() . $this->createUrl('Quiz:Quiz@historyAction', [$slug])
            ));
        } else {
            // Invalid slug. Trigger 404
            return false;
        }
    }

    /**
     * Renders session history by its id
     * 
     * @param int $sessionId
     * @return string
     */
    public function sessionAction($sessionId)
    {
        return $this->view->render(self::QUIZ_TEMPLATE_SESSION, [
            'page' => $this->createEntity(),
            'items' => $this->getModuleService('sessionService')->fetchAll($sessionId)
        ]);
    }

    /**
     * Runs the initial test
     * 
     * @return string
     */
    public function indexAction()
    {
        $quizTracker = $this->getModuleService('quizTracker');
        $categoryService = $this->getModuleService('categoryService');

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

        // Set current category name
        $page->setCategoryName($categoryService->fetchNameById($quizTracker->getCurrentCategoryId()));

        // If $id is false, then there's no more questions to be shown
        // Or if the provided limit exceeds the current track count
        if (!$id || ($this->getLimit($quizTracker->getCurrentCategoryId()) !== null && $this->getLimit($quizTracker->getCurrentCategoryId()) < $quizTracker->getCurrentCount())) {
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
        $categoryService = $this->getModuleService('categoryService');

        $categoryId = $quizTracker->getNextCategoryId();

        // Continue, if found a category
        if ($categoryId !== null) {
            $count = $questionService->countQuestionsByCategoryId($categoryId, $this->getLimit($categoryId));

            $quizTracker->setCurrentCategoryId($categoryId);
            $quizTracker->start($count);
            $quizTracker->resetCount(); // Reset trucking number count

            $page = $this->createEntity();
            $page->setCategoryName($categoryService->fetchNameById($quizTracker->getCurrentCategoryId()));

            $id = $this->getQuestionId($quizTracker->getCurrentCategoryId());
            return $this->quizAction($page, $id);

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
            $this->getModuleService('sessionService')->start();

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
                $count = $questionService->countQuestionsByCategoryId($categoryId, $this->getLimit($categoryId));

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

        // First priority
        $quizTracker->excludeCategoryId($quizTracker->getCurrentCategoryId());

        // Whether can continue
        $canContinue = $this->canContinue();
        $scores = $this->getModuleService('categoryService')->fetchResultset($quizTracker->getCorrectQuestionIds());

        // Indicate stopping, if can't go on
        if (!$canContinue) {
            $quizTracker->stop();

            // Keep the track
            $history = $this->getModuleService('historyService')->track(array_merge($quizTracker->getMeta(), array(
                'points' => $points,
                'content' => json_encode($scores)
            )));

            $this->view->addVariables([
                'history' => $history,
                // Current URL of the entry
                'url' => $this->request->getBaseUrl() . $this->createUrl('Quiz:Quiz@historyAction', [$history['slug']])
            ]);
        }

        return $this->view->render(self::QUIZ_TEMPLATE_RESULT, array(
            'meta' => $quizTracker->getMeta(),
            'takenTime' => $quizTracker->getTakenTime(),
            'points' => $points,
            'page' => $page,
            'canContinue' => $canContinue,
            'scores' => $scores
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
        $categoryService = $this->getModuleService('categoryService');
        $questionService = $this->getModuleService('questionService');
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

            // Track the response
            $this->getModuleService('sessionService')->trackResponse(
                $this->getModuleService('answerService')->fetchAll($questionId, true),
                $answerIds
            );

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
        $questionService = $this->getModuleService('questionService');
        $categoryService = $this->getModuleService('categoryService');

        $data = $this->createPair($id);

        // Track the render
        $this->getModuleService('sessionService')->trackRender(
            $categoryService->fetchNameById($quizTracker->getCurrentCategoryId()),
            $questionService->fetchQuestionById($id)
        );

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
     * @param int $categoryId
     * @return mixed
     */
    private function getLimit($categoryId = null)
    {
        static $limit = null; // Cache method calls

        // Is this a first call?
        if (is_null($limit)) {
            // 1. If numeric id provided
            if (is_numeric($categoryId)) {
                // First check the category
                $categoryLimit = $this->getModuleService('categoryService')->fetchLimitById($categoryId);

                if (is_numeric($categoryLimit) && $categoryLimit > 0) {
                    $limit = $categoryLimit;
                    return $categoryLimit;
                }
            }

            // 2. If category limit is not provided, then start a global limit lockup
            $globalLimit = $this->getModuleService('configManager')
                          ->getEntity()
                          ->getLimit();

            if (is_numeric($globalLimit) && $globalLimit > 0) {
                $limit = $globalLimit;
                return $globalLimit;
            } else {
                // No limit can be found
                return null;
            }

        } else {
            // Cached call
            return $limit;
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
     * @param string $categoryName
     * @return \Krystal\Stdlib\VirtualEntity
     */
    private function createEntity($categoryName = null)
    {
        $page = new VirtualEntity(false);
        $page->setSeo(false)
             ->setTitle($this->translator->translate('Passing the quiz'))
             ->setCategoryName($categoryName);

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
