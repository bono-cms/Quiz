<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Controller\Admin;

use Cms\Controller\Admin\AbstractController;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Validate\Pattern;

final class Answer extends AbstractController
{
    /**
     * Renders default
     * 
     * @param string $id Question id
     * @return string
     */
    public function listAction($id)
    {
        $question = $this->getModuleService('questionService')->fetchQuestionById($id);

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Quiz', 'Quiz:Admin:Browser@indexAction')
                                       ->addOne($this->translator->translate('Answers for "%s"', $question));

        return $this->view->render('answers', array(
            'answers' => $this->getModuleService('answerService')->fetchAll($id, false),
            'question' => $question,
            'id' => $id
        ));
    }

    /**
     * Create form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $answer
     * @param string $id Question id
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $answer, $id, $title)
    {
        $question = (string) $this->getModuleService('questionService')->fetchQuestionById($id);

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Quiz', 'Quiz:Admin:Browser@indexAction')
                                       ->addOne($this->translator->translate('Answers for "%s"', $question), $this->createUrl('Quiz:Admin:Answer@listAction', array($id)))
                                       ->addOne($title);

        return $this->view->render('answer.form', array(
            'answer' => $answer
        ));
    }

    /**
     * Renders adding form
     * 
     * @param string $id Question id
     * @return string
     */
    public function addAction($id)
    {
        $answer = new VirtualEntity();
        $answer->setQuestionId($id);

        return $this->createForm($answer, $id, 'Add new answer');
    }

    /**
     * Renders edit form
     * 
     * @param string $id Answer id
     * @return string
     */
    public function editAction($id)
    {
        $answer = $this->getModuleService('answerService')->fetchById($id);        

        if ($answer !== false) {
            return $this->createForm($answer, $answer->getQuestionId(), 'Edit the answer');
        } else {
            return false;
        }
    }

    /**
     * Save answer
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('answer');

        return $this->invokeSave('answerService', $input['id'], $this->request->getPost(), array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'answer' => new Pattern\Name()
                )
            )
        ));
    }

    /**
     * Delete an answer
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        return $this->invokeRemoval('answerService', $id);
    }
}
