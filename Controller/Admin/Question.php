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

        return $this->view->render('question.form', array(
            'question' => $question,
            'categories' => $this->getModuleService('categoryService')->fetchList()
        ));
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
        return $this->invokeRemoval('questionService', $id);
    }

    /**
     * Saves the changes
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('question');

        return $this->invokeSave('questionService', $input['id'], $this->request->getPost(), array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'question' => new Pattern\Name()
                )
            )
        ));
    }

}
