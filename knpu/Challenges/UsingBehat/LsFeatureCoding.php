<?php

namespace Challenges\UsingBehat;

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

class LsFeatureCoding implements CodingChallengeInterface
{
    /**
     * @return string
     */
    public function getQuestion()
    {
        return <<<EOF
Linus just told us about an edge case with the `ls` command:
it does *not* show files starting with a `.` by default.
To test for this, we've added two new scenarios that make sure
that a `.dino` file only shows up with the `-a` option.

Execute Behat from the command line, copy in the new step definition,
and then fill in the contents so that our scenario passes.
EOF;
    }

    public function getChallengeBuilder()
    {
        $fileBuilder = new ChallengeBuilder();
        $fileBuilder
            ->addFileContents('ls.feature', <<<EOF
Feature: ls
  In order to see the directory structure
  As a UNIX user
  I need to be able to list the current directory's contents
EOF
            )
            ->addFileContents('features/bootstrap/FeatureContext.php', <<<EOF
<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

class FeatureContext implements Context, SnippetAcceptingContext
{
    private \$output;

    /**
     * @Given I have a file named :filename
     */
    public function iHaveAFileNamed(\$filename)
    {
        touch(\$filename);
    }

    /**
     * @When I run :command
     */
    public function iRun(\$command)
    {
        \$this->output = shell_exec(\$command);
    }

    /**
     * @Then I should see :string in the output
     */
    public function iShouldSeeInTheOutput(\$string)
    {
        if (strpos(\$this->output, \$string) === false) {
            throw new \Exception(sprintf('Did not see "%s" in output "%s"', \$string, \$this->output));
        }
    }
}
EOF
            , true)
            ->setEntryPointFilename('ls.feature')
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

        $feature = $gherkin->getFeature('ls.feature');

        $scenarios = $feature->getScenarios();

        if (empty($scenarios)) {
            throw new GradingException('I don\'t see *any* scenarios. Check your syntax on the scenario');
        }

        $countScenarios = count($scenarios);
        if (2 !== $countScenarios) {
            throw new GradingException(
                sprintf('Make sure to create only a *2* new scenarios. Looks like *%d* was created.', $countScenarios)
            );
        }

        /** @var ScenarioNode $scenario */
        foreach ($scenarios as $index => $scenario) {
            $scenarioNumber = $index + 1;

            if (!$scenario->getTitle()) {
                throw new GradingException(sprintf(
                    'Make sure to put a short title after your scenario %d.',
                    $scenarioNumber
                ));
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
                throw new GradingException(sprintf(
                    'I don\'t see a `Given` in your scenario %d: you probably want one to have a hidden file starting with `.`.',
                    $scenarioNumber
                ));
            }
            if (!$hasWhen) {
                throw new GradingException(sprintf(
                    'I don\'t see a `When` in your scenario %d: you definitely need some, like for running commands in the terminal.',
                    $scenarioNumber
                ));
            }
            if (!$hasThen) {
                throw new GradingException(sprintf(
                    'I don\'t see a `Then` in your scenario %d: you probably want one where you check if the success message was shown.',
                    $scenarioNumber
                ));
            }
        }
    }

    public function configureCorrectAnswer(CorrectAnswer $correctAnswer)
    {
        $correctAnswer
            ->setFileContents('ls.feature', <<<EOF
Feature: ls
  In order to see the directory structure
  As a UNIX user
  I need to be able to list the current directory's contents

  Scenario: Do not list hidden files without "-a" option
    Given I have a file named ".dino"
    When I run "ls"
    Then I should see "" in the output

  Scenario: List hidden files with "-a" option
    Given I have a file named ".dino"
    When I run "ls -a"
    Then I should see ".dino" in the output
EOF
            )
        ;
    }
}
