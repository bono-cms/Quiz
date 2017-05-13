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

final class Question extends AbstractController
{
    /**
     * Renders a form
     * 
     * @oaram \Krystal\Stdlib\VirtualEntity $question
     * @param string $title     
     * @return string
     */
    private function createForm(VirtualEntity $question, $title)
    {
        $this->view->getBreadcrumbBag()->addOne('Quiz', 'Quiz:Admin:Browser@indexAction')
                                       ->addOne($title);

        // Load WYSIWYG editor in view
        $this->view->getPluginBag()
                   ->load($this->getWysiwygPluginName());

        return $this->view->render('question.form', array(
            'question' => $question,
            'categories' => $this->getModuleService('categoryService')->fetchList()
        ));
    }

    /**
     * Saves configuration
     * 
     * @return string
     */
    public function tweakAction()
    {
        if ($this->request->hasPost('order')) {
            $orders = $this->request->getPost('order');

            if ($this->getModuleService('questionService')->updateOrders($orders)) {
                $this->flashBag->set('success', 'Settings have been updated successfully');
                return '1';
            }
        }
    }

    /**
     * Renders adding form
     * 
     * @param string $id Category id
     * @return string
     */
    public function addAction($id)
    {
        $question = new VirtualEntity();
        $question->setCategoryId($id);

        return $this->createForm($question, 'Add new question');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $question = $this->getModuleService('questionService')->fetchById($id);

        if ($question !== false) {
            return $this->createForm($question, 'Edit the question');
        } else {
            return false;
        }
    }

    /**
     * Deletes a question
     * 
     * @param string $id Question id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('questionService');

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

    /**
     * Saves the changes
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('question');

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'question' => new Pattern\Name()
                )
            )
        ));

        if ($formValidator->isValid()) {
            $service = $this->getModuleService('questionService');

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
}
