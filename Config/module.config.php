<?php

/**
 * Module configuration container
 */

return array(
    'name'  => 'Quiz',
    'description' => 'Lets you manage online quizes on your site',
    'menu' => array(
        'name' => 'Quiz',
        'icon' => 'fas fa-chart-area',
        'items' => array(
            array(
                'route' => 'Quiz:Admin:Browser@indexAction',
                'name' => 'View all'
            ),
            array(
                'route' => 'Quiz:Admin:Question@addAction',
                'name' => 'Add new question'
            ),
            array(
                'route' => 'Quiz:Admin:Config@indexAction',
                'name' => 'Configuration'
            ),
            array(
                'route' => 'Quiz:Admin:Category@addAction',
                'name' => 'Add new category'
            ),
            array(
                'route' => 'Quiz:Admin:History@indexAction',
                'name' => 'History'
            )
        )
    )
);