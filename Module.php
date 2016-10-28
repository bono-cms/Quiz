<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Quiz;

use Cms\AbstractCmsModule;
use Quiz\Service\CategoryService;
use Quiz\Service\QuestionService;

final class Module extends AbstractCmsModule
{
    /**
     * {@inheritDoc}
     */
    public function getServiceProviders()
    {
        return array(
            'questionService' => new QuestionService($this->getMapper('\Quiz\Storage\MySQL\QuestionMapper')),
            'categoryService' => new CategoryService($this->getMapper('\Quiz\Storage\MySQL\CategoryMapper'))
        );
    }
}
