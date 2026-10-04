<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return [
    '/quiz/history/(:var)' => [
        'controller' => 'Quiz@historyAction'
    ],

    '/quiz/session/(:var)' => [
        'controller' => 'Quiz@sessionAction'
    ],
    
    '/quiz/continue' => [
        'controller' => 'Quiz@continueAction'
    ],

    '/quiz' => [
        'controller' => 'Quiz@indexAction'
    ],
    
    '/quiz/abort' => [
        'controller' => 'Quiz@abortAction'
    ],
    
    '/%s/module/quiz' => [
        'controller' => 'Admin:Browser@indexAction'
    ],

    '/%s/module/quiz/history' => [
        'controller' => 'Admin:History@indexAction'
    ],

    '/%s/module/quiz/history/delete' => [
        'controller' => 'Admin:History@deleteAction'
    ],
    
    '/%s/module/quiz/history/filter/(:var)' => [
        'controller' => 'Admin:History@filterAction'
    ],

    '/%s/module/quiz/history/page/(:var)' => [
        'controller' => 'Admin:History@indexAction'
    ],
    
    '/%s/module/quiz/category/view/(:var)/page/(:var)' => [
        'controller' => 'Admin:Browser@categoryAction'
    ],
    
    // Category
    '/%s/module/quiz/category/view/(:var)' => [
        'controller' => 'Admin:Browser@categoryAction'
    ],
    
    '/%s/module/quiz/category/add' => [
        'controller' => 'Admin:Category@addAction'
    ],
    
    '/%s/module/quiz/category/edit/(:var)' => [
        'controller' => 'Admin:Category@editAction'
    ],
    
    '/%s/module/quiz/category/save' => [
        'controller' => 'Admin:Category@saveAction'
    ],

    '/%s/module/quiz/category/delete/(:var)' => [
        'controller' => 'Admin:Category@deleteAction'
    ],
    
    // Question
    '/%s/module/quiz/question/add/(:var)' => [
        'controller' => 'Admin:Question@addAction'
    ],
    
    '/%s/module/quiz/question/edit/(:var)' => [
        'controller' => 'Admin:Question@editAction'
    ],
    
    '/%s/module/quiz/question/save' => [
        'controller' => 'Admin:Question@saveAction'
    ],
    
    '/%s/module/quiz/question/tweak' => [
        'controller' => 'Admin:Question@tweakAction'
    ],

    '/%s/module/quiz/question/delete/(:var)' => [
        'controller' => 'Admin:Question@deleteAction'
    ],
    
    // Answers
    '/%s/module/quiz/question/answers/(:var)' => [
        'controller' => 'Admin:Answer@listAction'
    ],
    
    '/%s/module/quiz/answer/edit/(:var)' => [
        'controller' => 'Admin:Answer@editAction'
    ],
    
    '/%s/module/quiz/answer/save' => [
        'controller' => 'Admin:Answer@saveAction'
    ],
    
    '/%s/module/quiz/answer/delete/(:var)' => [
        'controller' => 'Admin:Answer@deleteAction'
    ],

    // Configuration
    '/%s/module/quiz/config' => [
        'controller' => 'Admin:Config@indexAction'
    ],

    '/%s/module/quiz/config/save' => [
        'controller' => 'Admin:Config@saveAction',
        'disallow' => ['guest']
    ]
];