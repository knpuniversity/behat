<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $output;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given I have a file named :filename
     */
    public function iHaveAFileNamed($filename)
    {
        touch($filename);
    }

    /**
     * @When I run :command
     */
    public function iRun($command)
    {
        $this->output = shell_exec($command);
    }

    /**
     * @Then I should see :string in the output
     */
    public function iShouldSeeInTheOutput($string)
    {
        assertContains(
            $string,
            $this->output,
            sprintf('Did not see "%s" in output "%s"', $string, $this->output)
        );
    }

    /**
     * @BeforeScenario
     */
    public function moveIntoTestDir()
    {
        if (!is_dir('test')) {
            mkdir('test');
        }
        chdir('test');
    }

    /**
     * @AfterScenario
     */
    public function moveOutOfTestDir()
    {
        chdir('..');
        if (is_dir('test')) {
            system('rm -r '.realpath('test'));
        }
    }

    /**
     * @Given I have a dir named :dirName
     */
    public function iHaveADirNamed($dirName)
    {
        mkdir($dirName);
    }

    /**
     * @When I fill in the search box with :arg1
     */
    public function iFillInTheSearchBoxWith($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I press the search button
     */
    public function iPressTheSearchButton()
    {
        throw new PendingException();
    }
}
