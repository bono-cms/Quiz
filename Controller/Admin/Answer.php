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
     * Creates a grid
     * 
     * @param string $id
     * @param \Krystal\Stdlib\VirtualEntity $answer
     * @return string
     */
    private function createGrid($id, VirtualEntity $answer)
    {
        $question = $this->getModuleService('questionService')->fetchQuestionById($id);

        // Trigger not found, if wrong id supplied
        if ($question === false) {
            return false;
        }

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Quiz', 'Quiz:Admin:Browser@indexAction')
                                       ->addOne($this->translator->translate('Answers for "%s"', (string)$question));

        return $this->view->render('answers', array(
            'answers' => $this->getModuleService('answerService')->fetchAll($id, false),
            'question' => $question,
            'answer' => $answer,
            'id' => $id
        ));
    }

    /**
     * Renders default
     * 
     * @param string $id Question id
     * @return string
     */
    public function listAction($id)
    {
        $answer = new VirtualEntity();
        $answer->setQuestionId($id);

        return $this->createGrid($id, $answer);
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
            return $this->createGrid($answer->getQuestionId(), $answer);
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

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'answer' => new Pattern\Name()
                )
            )
        ));

        if ($formValidator->isValid()) {
            $service = $this->getModuleService('answerService');

            if (!empty($input['id'])) {
                if ($service->update($this->request->getPost())) {
                    $this->flashBag->set('success', 'The element has been updated successfully');
                    return '1';
                }

            } else {
                if ($service->add($this->request->getPost())) {
                    $this->flashBag->set('success', 'The element has been created successfully');
                    return $service->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }

    /**
     * Delete an answer
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('answerService');

        // Batch removal
        if ($this->request->hasPost('toDelete')) {
            $ids = array_keys($this->request->getPost('toDelete'));

            $service->deleteByIds($ids);
            $this->flashBag->set('success', 'Selected elements have been removed successfully');

        } else {
            $this->flashBag->set('warning', 'You should select at least one element to remove');
        }

        // Single removal
        if (!empty($id)) {
            $service->deleteById($id);
            $this->flashBag->set('success', 'Selected element has been removed successfully');
        }

        return '1';
    }
}
