<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz\Controller\Admin;

use Cms\Controller\Admin\AbstractController;
use Krystal\Stdlib\VirtualEntity;

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

        return $this->view->render('category.form', [
            'category' => $category
        ]);
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

        return $this->json([
            'refresh' => true
        ]);
    }

    /**
     * Renders category
     * 
     * @return string
     */
    public function addAction()
    {
        $entity = new VirtualEntity();
        $entity->setMark(1); // By default

        return $this->createForm($entity, 'Add new category');
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
            return $this->createForm($category, $this->translator->translate('Edit the category "%s"', $category->getName()));
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

        $validator = $this->createValidation();

        $validator->field('category.name')
                  ->required();

        $validator->field('category.order')
                  ->addRule('numeric');

        $validator->field('category.mark')
                  ->addRule('numeric');

        $validator->field('category.limit')
                  ->addRule('numeric');

        if ($validator->isPassed()) {
            $service = $this->getModuleService('categoryService');

            // Update
            if (!empty($input['id'])) {
                if ($service->update($input)) {
                    $this->flashBag->set('success', 'The element has been updated successfully');

                    return $this->json([
                        'refresh' => true
                    ]);
                }

            } else {
                // Create
                if ($service->add($input)) {
                    $this->flashBag->set('success', 'The element has been created successfully');

                    return $this->json([
                        'redirect' => $this->createUrl('Quiz:Admin:Category@editAction', [$service->getLastId()]),
                    ]);
                }
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }
}
