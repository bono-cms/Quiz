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
            // If the welcoming form was submitted, then grab and save its value and start tracking
            if ($this->request->hasPost('category')) {
                // Initial loading from request
                $categoryId = $this->request->getPost('category');
                $ids = $this->getModuleService('questionService')->fetchQuiestionIdsByCategoryId($categoryId);

                $quizTracker->start($ids);
                $quizTracker->saveMeta(array(
                    'name' => $this->request->getPost('name'),
                    'category' => $this->getModuleService('categoryService')->fetchNameById($categoryId)
                ));

            } else {
                // In case that was the first GET request, render welcome page
                return $this->view->render('welcome', array(
                    'categories' => $this->getModuleService('categoryService')->fetchList(),
                    'page' => $page
                ));
            }

        } else {
            // Answer page
            if ($this->request->isPost()) {
                $questionId = $this->request->getPost('question');

                // Keep track of corectness
                foreach ($this->request->getPost('answerIds', array()) as $answerId) {
                    $correct = $this->getModuleService('answerService')->isCorrect($questionId, $answerId);

                    if ($correct) {
                        $quizTracker->appendCorrectQuestionId($questionId);
                    }
                }
            }
        }

        $id = $quizTracker->createQuestionId();

        // If $id is false, then there's no more questions to be shown
        if ($id === false) {
            // Do save track only in case the stopping has been indicated
            if (!$quizTracker->isStopped()) {
                // Keep the track
                $this->getModuleService('historyService')->track(array_merge($quizTracker->getMeta(), array(
                    'timestamp' => time(),
                    'points' => $quizTracker->getCorrectAnsweredCount()
                )));
            }

            // Indicate stopping
            $quizTracker->stop();

            return $this->view->render('result', array(
                'meta' => $quizTracker->getMeta(),
                'points' => $quizTracker->getCorrectAnsweredCount(),
                'page' => $page,
            ));
        }

        $data = $this->createPair($id);

        return $this->view->render('quiz', array_merge($data, array(
            'page' => $page,
            'hasManyCorrectAnswers' => $this->getModuleService('answerService')->hasManyCorrectAnswers($data['answers']),
            'initialCount' => $quizTracker->getInitialCount(),
            'currentQuestionCount' => $quizTracker->getCurrentQuestionCount()
        )));
    }
}
