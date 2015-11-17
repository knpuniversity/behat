<?php

namespace Challenges\Scenarios;

use KnpU\Gladiator\MultipleChoice\AnswerBuilder;
use KnpU\Gladiator\MultipleChoiceChallengeInterface;

class BadScenarioMC implements MultipleChoiceChallengeInterface
{
    /**
     * @return string
     */
    public function getQuestion()
    {
        return <<<EOF
Check out the following scenario. What's wrong here?

```gherkin
Scenario: Sending a message
  Given there is a "dr_dino@example.com" user in the database
  And I am logged in as "dr_dino@example.com"
  And I am on "/"
  When I click "Send Message"
  And I fill in the message box with "Hello everyone!"
  And I press "Send"
  Then a message from "dr_dino@example.com" should be sent
```
EOF;
    }

    /**
     * @param AnswerBuilder $builder
     */
    public function configureAnswers(AnswerBuilder $builder)
    {
        $builder
            ->addAnswer('Actually, this scenario is perfect!')
            ->addAnswer('In `Given`, you can only do things that the user can do. So, you can\'t magically "add a user to the database"')
            ->addAnswer('In `Given`, you can\'t take user action. Saying "I am logged in as" implies that the user will go to the login page and login. This is user action and so should be under `When`.')
            ->addAnswer('In `Then`, the user cannot see that a "message should be sent". The user would see some sort of a message, like "Your message has been sent" and we should look for this.', true)
        ;
    }

    /**
     * @return string
     */
    public function getExplanation()
    {
        return <<<EOF
In `Given`, you *can* "play god" and do things like database setup that your
user cannot do. That's it's biggest purpose. But, it's also "ok" to take some
user action - like logging in - when that user action really isn't central
to what your scenario is really trying to do.

The real problem is the `Then`: it is describing something that the user
cannot see. What does it mean that the message "should be sent"? Unlike `Given`,
we can't give the user super-powers here and allow them to "see" technical
things, like messages being sent or database records being inserted.
EOF;
    }
}
