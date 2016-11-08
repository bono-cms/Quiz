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
use Krystal\Form\Gadget\LastCategoryKeeper;

final class Browser extends AbstractController
{
    /**
     * Returns category keeper service
     * 
     * @return \Krystal\Form\Gadget\LastCategoryKeeper
     */
    private function getCategoryIdKeeper()
    {
        static $keeper = null;

        if (is_null($keeper)) {
            $keeper = new LastCategoryKeeper($this->sessionBag, 'last_quiz_category_id');
        }

        return $keeper;
    }

    /**
     * Creates the grid
     * 
     * @param string $id Category id
     * @param string $page Page number
     * @return string
     */
    private function createGrid($id, $page)
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne('Quiz');

        $paginator = $this->getModuleService('questionService')->getPaginator();
        $paginator->setUrl($this->createUrl('Quiz:Admin:Browser@categoryAction', array($id), 0));

        return $this->view->render('browser', array(
            'categoryId' => $id,
            'questions' => $this->getModuleService('questionService')->fetchAllByCategoryId($id, $page, $this->getSharedPerPageCount()),
            'categories' => $this->getModuleService('categoryService')->fetchAll(false),
            'paginator' => $paginator
        ));
    }

    /**
     * Renders the grid filtering by category id
     * 
     * @param string $id Category id
     * @param string $page Page number
     * @return string
     */
    public function categoryAction($id, $page = 1)
    {
        // Remember category id
        $this->getCategoryIdKeeper()->persistLastCategoryId($id);

        return $this->createGrid($id, $page);
    }

    /**
     * Renders the grid
     * 
     * @param string $page Page number
     * @return string
     */
    public function indexAction()
    {
        if ($this->getCategoryIdKeeper()->hasLastCategoryId()) {
            $id = $this->getCategoryIdKeeper()->getLastCategoryId();
        } else {
            $id = $this->getModuleService('categoryService')->getLastId();
        }

        return $this->createGrid($id, 1);
    }
}
