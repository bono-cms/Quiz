TODO
====

* Add custom callback function to handle marks for categories
* Add difficulty levels to questions with corresponding filter
* Bug in History with filter button
* Form validation [ AJAX ]
* Fix printable version
* Implement resulting rules ( with configuration - whether mark or points should be used )
* Removal in relations (Foreign keys)
* BUG: When switching language in admin panel, all questions remain the same

ROADMAP
=======

* Option to enable and disable back/next buttons
* Payment integration
* Export to Excel
* Text answers (match against pre-defined set of answers)
* Image, audio and YouTube links in questions
* Schedule
* Individual marks for questions
* Easier localization
* Restrictions on how many times test can be passed
* Quiz API
* Math formulas in answers (images for now)
* Published and non-published categories
* Certificates
* Ability to count questions with no answers using the following query

`SELECT * FROM bono_module_quiz_questions
    LEFT OUTER JOIN bono_module_quiz_answers
    ON bono_module_quiz_answers.question_id = bono_module_quiz_questions.id
    WHERE bono_module_quiz_answers.question_id IS NULL`