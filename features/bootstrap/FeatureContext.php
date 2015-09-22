<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
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
     * @Given I have a file named :arg1
     */
    public function iHaveAFileNamed($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I run :arg1
     */
    public function iRun($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then I should see :arg1 in the output
     */
    public function iShouldSeeInTheOutput($arg1)
    {
        throw new PendingException();
    }
}
