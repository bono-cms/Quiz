<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return array(
    
    '/quiz' => array(
        'controller' => 'Quiz@indexAction'
    ),
    
    '/quiz/abort' => array(
        'controller' => 'Quiz@abortAction'
    ),
    
    '/%s/module/quiz' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),

    '/%s/module/quiz/history' => array(
        'controller' => 'Admin:History@indexAction'
    ),

    '/%s/module/quiz/history/delete' => array(
        'controller' => 'Admin:History@deleteAction'
    ),
    
    '/%s/module/quiz/history/filter/(:var)' => array(
        'controller' => 'Admin:History@filterAction'
    ),

    '/%s/module/quiz/history/page/(:var)' => array(
        'controller' => 'Admin:History@indexAction'
    ),
    
    '/%s/module/quiz/category/view/(:var)/page/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    // Category
    '/%s/module/quiz/category/view/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    '/%s/module/quiz/category/add' => array(
        'controller' => 'Admin:Category@addAction'
    ),
    
    '/%s/module/quiz/category/edit/(:var)' => array(
        'controller' => 'Admin:Category@editAction'
    ),
    
    '/%s/module/quiz/category/save' => array(
        'controller' => 'Admin:Category@saveAction'
    ),

    '/%s/module/quiz/category/delete/(:var)' => array(
        'controller' => 'Admin:Category@deleteAction'
    ),
    
    // Question
    '/%s/module/quiz/question/add/(:var)' => array(
        'controller' => 'Admin:Question@addAction'
    ),
    
    '/%s/module/quiz/question/edit/(:var)' => array(
        'controller' => 'Admin:Question@editAction'
    ),
    
    '/%s/module/quiz/question/save' => array(
        'controller' => 'Admin:Question@saveAction'
    ),
    
    '/%s/module/quiz/question/tweak' => array(
        'controller' => 'Admin:Question@tweakAction'
    ),

    '/%s/module/quiz/question/delete/(:var)' => array(
        'controller' => 'Admin:Question@deleteAction'
    ),
    
    // Answers
    '/%s/module/quiz/question/answers/(:var)' => array(
        'controller' => 'Admin:Answer@listAction'
    ),
    
    '/%s/module/quiz/answer/edit/(:var)' => array(
        'controller' => 'Admin:Answer@editAction'
    ),
    
    '/%s/module/quiz/answer/save' => array(
        'controller' => 'Admin:Answer@saveAction'
    ),
    
    '/%s/module/quiz/answer/delete/(:var)' => array(
        'controller' => 'Admin:Answer@deleteAction'
    ),

    // Configuration
    '/%s/module/quiz/config' => array(
        'controller' => 'Admin:Config@indexAction'
    ),

    '/%s/module/quiz/config/save' => array(
        'controller' => 'Admin:Config@saveAction',
        'disallow' => array('guest')
    )
);
