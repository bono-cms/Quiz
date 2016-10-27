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
    '/%s/module/quiz' => array(
        'controller' => 'Admin:Browser@indexAction'
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
    )
);
