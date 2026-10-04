<?php

/**
 * Module configuration container
 */

return [
    'name'  => 'Quiz',
    'description' => 'Lets you manage online quizes on your site',
    'menu' => [
        'name' => 'Quiz',
        'icon' => 'fas fa-chart-area',
        'items' => [
            [
                'route' => 'Quiz:Admin:Browser@indexAction',
                'name' => 'View all'
            ],
            [
                'route' => 'Quiz:Admin:Question@addAction',
                'name' => 'Add new question'
            ],
            [
                'route' => 'Quiz:Admin:Config@indexAction',
                'name' => 'Configuration'
            ],
            [
                'route' => 'Quiz:Admin:Category@addAction',
                'name' => 'Add new category'
            ],
            [
                'route' => 'Quiz:Admin:History@indexAction',
                'name' => 'History'
            ]
        ]
    ]
];