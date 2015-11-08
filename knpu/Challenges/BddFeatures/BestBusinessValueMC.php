<?php

namespace Challenges\BddFeatures;

use KnpU\Gladiator\MultipleChoice\AnswerBuilder;
use KnpU\Gladiator\MultipleChoiceChallengeInterface;

class BestBusinessValueMC implements MultipleChoiceChallengeInterface
{
    /**
     * @return string
     */
    public function getQuestion()
    {
        return <<<EOF
For the messaging feature, you and the intern (Bob) are in
a heated debate over how to phrase the business value.
Which if the following is the *best* business value for the
message feature:
EOF;
    }

    /**
     * @param AnswerBuilder $builder
     */
    public function configureAnswers(AnswerBuilder $builder)
    {
        $builder
            ->addAnswer('In order to type messages and click to send them to scientists.')
            ->addAnswer('In order to communicate with other scientists.')
            ->addAnswer('In order to exchange information and resources with other scientists.', true)
            ->addAnswer('In order to send cat videos.')
        ;
    }

    /**
     * @return string
     */
    public function getExplanation()
    {
        return <<<EOF
Business value is subjective. But to help, forget about the technology
and think about the problem that your user role (scientist) wants to
solve. Both "communicate" and "exchange information and resources"
are pretty good business values, but I like the second, because it
contains a few more details about what the scientists actually
want to do.
EOF;
    }
}
