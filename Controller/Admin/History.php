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

final class History extends AbstractController
{
    /**
     * Creates the grid
     * 
     * @param array $records
     * @param string $url
     * @return string
     */
    private function createGrid(array $records, $url = null)
    {
        // Configure breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Quiz', 'Quiz:Admin:Browser@indexAction')
                                       ->addOne('History');

        // Configure paginator's instance
        $paginator = $this->getModuleService('historyService')->getPaginator();

        if (!is_null($url)) {
            $paginator->setUrl($url);
        }

        return $this->view->render('history', array(
            'paginator' => $paginator,
            'records' => $records,
            'categories' => $this->getModuleService('categoryService')->fetchList(true),
            'filterApplied' => $this->request->getQuery('filter', false),
            'query' => $this->request->getQuery(),
            'route' => $this->createUrl('Quiz:Admin:History@filterAction', array(null))
        ));
    }

    /**
     * Deletes a history item or a collection of them
     * 
     * @return string
     */
    public function deleteAction()
    {
        $service = $this->getModuleService('historyService');

        // Batch removal
        if ($this->request->hasPost('batch')) {
            $ids = array_keys($this->request->getPost('batch'));

            $service->deleteByIds($ids);
            $this->flashBag->set('success', 'Selected elements have been removed successfully');

        } else {
            $this->flashBag->set('warning', 'You should select at least one element to remove');
        }

        return '1';
    }

    /**
     * Applies a filter
     * 
     * @return string
     */
    public function filterAction()
    {
        $records = $this->getFilter($this->getModuleService('historyService'), $this->createUrl('Quiz:Admin:History@filterAction', array(null)));

        if ($records !== false) {
            return $this->createGrid($records);
        } else {
            return $this->indexAction();
        }
    }

    /**
     * Renders the grid
     * 
     * @param integer $page
     * @return string
     */
    public function indexAction($page = 1)
    {
        $records = $this->getModuleService('historyService')->fetchAll($page, $this->getSharedPerPageCount());
        $url = $this->createUrl('Quiz:Admin:History@indexAction', array(), 1);

        return $this->createGrid($records, $url);
    }
}
