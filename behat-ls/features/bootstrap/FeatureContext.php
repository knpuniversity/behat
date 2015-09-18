<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';
//

use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\When;
use Behat\Behat\Context\Step\Then;

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    private $output;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }


    /**
     * @Given /^I have a file named "([^"]*)"$/
     */
    public function iHaveAFileNamed($filename)
    {
        touch($filename);
    }

    /**
     * @Given /^I have a dir named "([^"]*)"$/
     */
    public function iHaveADirNamed($dir)
    {
        mkdir($dir);
    }

    /**
     * @When /^I run "([^"]*)"$/
     */
    public function iRun($command)
    {
        exec($command, $this->output);
    }

    /**
     * @Then /^I should see "([^"]*)" in the output$/
     */
    public function iShouldSeeInTheOutput($string)
    {
        assertContains($string, $this->output, sprintf('Did not see "%s" in the output', $string));
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
     * @BeforeScenario
     */
    public function moveIntoTestDir()
    {
        mkdir('test');
        chdir('test');
    }


    /**
     * @When /^I fill in the search box with "([^"]*)"$/
     */
    public function iFillInTheSearchBoxWith($searchText)
    {
        return new When(sprintf('I fill in "search" with "%s"', $searchText));
    }

    /**
     * @Given /^I press the search button$/
     */
    public function iPressTheSearchButton()
    {
        return new When('I press "searchButton"');
    }

    /**
     * @Given /^I wait for the suggestions box to appear$/
     */
    public function iWaitForTheSuggestionsBoxToAppear()
    {
        $this->getSession()->wait(
            5000,
            "$('.suggestions-results').children().length > 0"
        );
    }

    /**
     * @return Behat\Mink\Element\DocumentElement
     */
    protected function getPage()
    {
        return $this->getSession()->getPage();
    }
}
