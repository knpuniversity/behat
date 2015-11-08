<?php

namespace Challenges\UsingBehat;

use KnpU\Gladiator\MultipleChoice\AnswerBuilder;
use KnpU\Gladiator\MultipleChoiceChallengeInterface;

class IndependentScenariosMC implements MultipleChoiceChallengeInterface
{
    /**
     * @return string
     */
    public function getQuestion()
    {
        return <<<EOF
The intern Bob is writing some scenarios that describe
the behavior of the account area of our paleontology app.
Do you see any problems with the second scenario?

```gherkin
  Scenario: Register
    Given I am on "/"
    When I fill in "email" with "dr_dino@example.com"
    And I fill in "password" with "roar"
    And I press "Register"
    Then I should see "You're registered!"

  Scenario: View my account area
    Given I am on "/login"
    When I fill in "email" with "dr_dino@example.com"
    And I fill in "password" with "roar"
    And I press "Login"
    And I click "My Account"
    Then I should see "My Account Information"
```
EOF;
    }

    /**
     * @param AnswerBuilder $builder
     */
    public function configureAnswers(AnswerBuilder $builder)
    {
        $builder
            ->addAnswer(<<<EOF
The second scenario shouldn't start on `/login`, it should start on `/`
and then you should click a "Login" link.
EOF
            )
            ->addAnswer(<<<EOF
Scenario 2 uses the `dr_dino@example.com` user from scenario 1, but each scenario
should act completely independent of each other.
EOF
            , true)
            ->addAnswer(<<<EOF
The second scenario shouldn't need to repeat the email address and password,
since it is already in the first scenario.
EOF
            )
        ;
    }

    /**
     * @return string
     */
    public function getExplanation()
    {
        return <<<EOF
Each scenario should act completely independent of other scenarios.
Right now, in order for scenario 2 to pass, you *must* run scenario 1
first. This makes your scenarios very fragile and difficult to debug.
Instead, the second scenario should make sure that the `dr_dino@example.com`
user is in the database via a `Given` statement. We'll talk more about
how to do this soon.
EOF;
    }
}
