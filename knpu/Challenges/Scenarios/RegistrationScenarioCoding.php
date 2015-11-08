<?php

namespace Challenges\Scenarios;

use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use KnpU\Gladiator\CodingChallenge\CodingContext;
use KnpU\Gladiator\CodingChallenge\CorrectAnswer;
use KnpU\Gladiator\CodingChallengeInterface;
use KnpU\Gladiator\CodingChallenge\CodingExecutionResult;
use KnpU\Gladiator\CodingChallenge\ChallengeBuilder;
use KnpU\Gladiator\CodingChallenge\Exception\GradingException;
use KnpU\Gladiator\Grading\GherkinGradingTool;
use KnpU\Gladiator\Worker\WorkerLoaderInterface;

class RegistrationScenarioCoding implements CodingChallengeInterface
{
    /**
     * @return string
     */
    public function getQuestion()
    {
        return <<<EOF
After writing the feature for registration, the whole team
(even Bob the intern!) is excited about BDD! Let's write the
first scenario for our feature where we register successfully.
After some conversation, you basically want it to work like this:

1. You start on the homepage
2. You click a link called "Register"
3. You fill out an "Email" field and a "Password" field
4. You press a "Register" button
5. You see some text on the page, like "Hi Dr. Paleontology Person! Welcome!"

Add a scenario that represents this, and compare your answer
to ours. The most important thing is that you're using natural
language.
EOF;
    }

    public function getChallengeBuilder()
    {
        $fileBuilder = new ChallengeBuilder();
        $fileBuilder
            ->addFileContents('registration.feature', <<<EOF
Feature: Registration
  In order to send and receive messages from scientists I know
  As a scientist
  I can register for a new account
EOF
            )
            ->setEntryPointFilename('registration.feature')
        ;

        return $fileBuilder;
    }

    public function getWorkerConfig(WorkerLoaderInterface $loader)
    {
        return $loader->load(__DIR__.'/../gherkin_worker.yml');
    }

    public function setupContext(CodingContext $context)
    {
    }

    public function grade(CodingExecutionResult $result)
    {
        $gherkin = new GherkinGradingTool($result);

        $feature = $gherkin->getFeature('registration.feature');

        $scenarios = $feature->getScenarios();

        if (empty($scenarios)) {
            throw new GradingException('I don\'t see *any* scenarios. Check your syntax on the scenario');
        }

        /** @var ScenarioNode $scenario */
        $scenario = $scenarios[0];
        if (!$scenario->getTitle()) {
            throw new GradingException('Make sure to put a short title after your scenario');
        }

        /** @var StepNode[] $steps */
        $steps = $scenario->getSteps();
        $hasGiven = false;
        $hasWhen = false;
        $hasThen = false;
        foreach ($steps as $step) {
            if ('Given' == $step->getType()) {
                $hasGiven = true;
            } elseif ('When' == $step->getType()) {
                $hasWhen = true;
            } elseif ('Then' == $step->getType()) {
                $hasThen = true;
            }
        }

        if (!$hasGiven) {
            throw new GradingException('I don\'t see a Given in your scenario: you probably want one to start on the "/" page');
        }
        if (!$hasWhen) {
            throw new GradingException('I don\'t see a When in your scenario: you definitely need some, like for clicking Register, filling out the fields and pressing the submit button');
        }
        if (!$hasThen) {
            throw new GradingException('I don\'t see a Then in your scenario: you probably want one where you check if the success message was shown');
        }
    }

    public function configureCorrectAnswer(CorrectAnswer $correctAnswer)
    {
        $correctAnswer
            ->setFileContents('registration.feature', <<<EOF
Feature: Registration
  In order to send and receive messages from scientists I know
  As a scientist
  I can register for a new account

  Scenario: Successfully register
    Given I am on "/"
    When I click "Register"
    And I fill in "Email" with "dr_dino@example.com"
    And I fill in "Password" with "r00000ar"
    And I press "Register"
    Then I should see "Hi Dr. Paleontology Person! Welcome!"
EOF
            )
        ;
    }
}
