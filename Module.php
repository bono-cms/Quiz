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
use Quiz\Service\AnswerService;
use Quiz\Service\CategoryService;
use Quiz\Service\QuestionService;
use Quiz\Service\QuizTracker;
use Quiz\Service\HistoryService;

final class Module extends AbstractCmsModule
{
    /**
     * {@inheritDoc}
     */
    public function getServiceProviders()
    {
        $questionMapper = $this->getMapper('\Quiz\Storage\MySQL\QuestionMapper');
        $categoryMapper = $this->getMapper('\Quiz\Storage\MySQL\CategoryMapper');
        $answerMapper = $this->getMapper('\Quiz\Storage\MySQL\AnswerMapper');
        $historyMapper = $this->getMapper('\Quiz\Storage\MySQL\HistoryMapper');

        return array(
            'configManager' => $this->createConfigService(),
            'historyService' => new HistoryService($historyMapper),
            'quizTracker' => new QuizTracker($this->getServiceLocator()->get('sessionBag')),
            'answerService' => new AnswerService($answerMapper),
            'questionService' => new QuestionService($questionMapper),
            'categoryService' => new CategoryService($categoryMapper, $questionMapper)
        );
    }
}
