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
                // Initial loading from request
                $categoryId = $this->request->getPost('category');
                $ids = $this->getModuleService('questionService')->fetchQuiestionIdsByCategoryId($categoryId);

                $quizTracker->start($ids);
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
            return $this->view->render('welcome', array(
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

        // Indicate stopping
        $quizTracker->stop();

        return $this->view->render('result', array(
            'meta' => $quizTracker->getMeta(),
            'takenTime' => $quizTracker->getTakenTime(),
            'points' => $points,
            'page' => $page
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
        $ids = $this->request->getPost('answerIds', array());
        $input['collection'] = $ids;

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
            // Keep track of correctness
            foreach ($ids as $answerId) {
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

        return $this->view->render('quiz', array_merge($data, array(
            'page' => $page,
            'hasManyCorrectAnswers' => $this->getModuleService('answerService')->hasManyCorrectAnswers($data['answers']),
            'initialCount' => $quizTracker->getInitialCount(),
            'currentQuestionCount' => $quizTracker->getCurrentQuestionCount(),
            'lastQuestion' => $quizTracker->getInitialCount() == $quizTracker->getCurrentQuestionCount()
        )));
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
     * Runs the initial test
     * 
     * @return string
     */
    public function indexAction()
    {
        $this->loadSitePlugins();

        // Configure view
        $this->view->setLayout('__layout__')
                   ->setModule('Quiz')
                   ->setTheme('site');

        $page = new \Krystal\Stdlib\VirtualEntity();

        $quizTracker = $this->getModuleService('quizTracker');

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

        $id = $quizTracker->createQuestionId();

        // If $id is false, then there's no more questions to be shown
        if ($id === false) {
            return $this->stopAction($page);
        }

        return $this->quizAction($page, $id);
    }
}
