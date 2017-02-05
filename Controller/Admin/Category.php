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

final class Category extends AbstractController
{
    /**
     * Creates category form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $category
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $category, $title)
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Quiz', 'Quiz:Admin:Browser@indexAction')
                   ->addOne($title);

        return $this->view->render('category.form', array(
            'category' => $category
        ));
    }

    /**
     * Deletes a category
     * 
     * @param string $id Category
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('categoryService');
        $service->deleteById($id);

        $this->flashBag->set('success', 'Selected element has been removed successfully');
        return '1';
    }

    /**
     * Renders category
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity(), 'Add new category');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $category = $this->getModuleService('categoryService')->fetchById($id);

        if ($category !== false) {
            return $this->createForm($category, 'Edit the category');
        } else {
            return false;
        }
    }

    /**
     * Persists a category
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('category');

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name()
                )
            )
        ));

        if ($formValidator->isValid()) {
            $service = $this->getModuleService('categoryService');

            // Update
            if (!empty($input['id'])) {
                if ($service->update($input)) {
                    $this->flashBag->set('success', 'The element has been updated successfully');
                    return '1';
                }

            } else {
                // Create
                if ($service->add($input)) {
                    $this->flashBag->set('success', 'The element has been created successfully');
                    return $service->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
