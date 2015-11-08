<?php

namespace Challenges\BehatHooksBackground;

use KnpU\Gladiator\CodingChallenge\CodingContext;
use KnpU\Gladiator\CodingChallenge\CorrectAnswer;
use KnpU\Gladiator\CodingChallengeInterface;
use KnpU\Gladiator\CodingChallenge\CodingExecutionResult;
use KnpU\Gladiator\CodingChallenge\ChallengeBuilder;
use KnpU\Gladiator\CodingChallenge\Exception\GradingException;
use KnpU\Gladiator\Grading\GherkinGradingTool;
use KnpU\Gladiator\Worker\WorkerLoaderInterface;

class afterStepHookCoding implements CodingChallengeInterface
{
    /**
     * @return string
     */
    public function getQuestion()
    {
        return <<<EOF
As a challenge, create a new function called `afterStepHook()` that
is called after *every* individual step in a scenario. Inside of
this, just execute `var_dump('After Step!');`.

**Hint**
See [hooking into the test process](http://docs.behat.org/en/v3.0/guides/3.hooks.html)
for the annotation needed to do things after every step.
EOF;
    }

    public function getChallengeBuilder()
    {
        $fileBuilder = new ChallengeBuilder();
        $fileBuilder
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
            )
            ->addFileContents('ls.feature', <<<EOF
Feature: ls
  In order to see the directory structure
  As a UNIX user
  I need to be able to list the current directory's contents

  Scenario: List 2 files in a directory
    Given I have a file named "john"
    And I have a file named "hammond"
    When I run "ls"
    Then I should see "john" in the output
    And I should see "hammond" in the output
EOF
                , true)
            ->setEntryPointFilename('features/bootstrap/FeatureContext.php')
        ;

        return $fileBuilder;
    }

    public function getWorkerConfig(WorkerLoaderInterface $loader)
    {
        return $loader->load(__DIR__.'/../gherkin_worker.yml');
    }

    public function setupContext(CodingContext $context)
    {
        // Create dummy interfaces if not exists that implemented by FeatureContext class.
        if (!class_exists('\Behat\Behat\Context\Context')) {
            eval(<<<EOF
namespace Behat\Behat\Context;

interface Context
{
}
EOF
            );
        }
        if (!class_exists('\Behat\Behat\Context\SnippetAcceptingContext')) {
            eval(<<<EOF
namespace Behat\Behat\Context;

interface SnippetAcceptingContext
{
}
EOF
            );
        }
    }

    public function grade(CodingExecutionResult $result)
    {
        $featureContextClass = new \ReflectionClass('FeatureContext');
        if (!$featureContextClass->hasMethod('afterStepHook')) {
            throw new GradingException('The `afterStepHook()` method wasn\'t found in the `FeatureContext` class.');
        }
        if (!$featureContextClass->getMethod('afterStepHook')->isPublic()) {
            throw new GradingException('The `afterStepHook()` method should be public.');
        }
        $docComment = $featureContextClass->getMethod('afterStepHook')->getDocComment();
        if (false === strpos($docComment, '@AfterStep')) {
            throw new GradingException('You should to use `@AfterStep` annotation for `afterStepHook()` method.');
        }
    }

    public function configureCorrectAnswer(CorrectAnswer $correctAnswer)
    {
        $correctAnswer
            ->setFileContents('features/bootstrap/FeatureContext.php', <<<EOF
<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

class FeatureContext implements Context, SnippetAcceptingContext
{
    private \$output;

    /**
     * @AfterStep
     */
    public function afterStepHook()
    {
        var_dump('After Step!');
    }

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
            )
        ;
    }
}
