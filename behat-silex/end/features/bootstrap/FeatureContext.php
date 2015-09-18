<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\When;
use Behat\Behat\Context\Step\Then;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    static private $app;

    private $currentUser;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @static
     * @BeforeSuite
     */
    static public function bootstrapSilex()
    {
        if (!self::$app) {
            self::$app = require __DIR__.'/../../app/bootstrap.php';
        }

        return self::$app;
    }

    /**
     * @return Behat\Mink\Element\DocumentElement
     */
    protected function getPage()
    {
        return $this->getSession()->getPage();
    }

    /**
     * @Given /^I am logged in as an admin$/
     */
    public function iAmLoggedInAsAnAdmin()
    {
        $this->currentUser = self::$app['user_repository']->createAdminUser(
            'admin',
            'adminpass'
        );

        return array(
            new Given('I am on "/login"'),
            new Given('I fill in "Username" with "admin"'),
            new Given('I fill in "Password" with "adminpass"'),
            new Given('I press "Login"'),
        );
    }

    /**
     * @Given /^there are (\d+) products$/
     */
    public function thereAreProducts($num)
    {
        for ($i = 0; $i < $num; $i++) {
            self::$app['product_repository']->createProduct(
                'Sickle-shaped Claw'.$num,
                9.99+$i
            );
        }
    }

    /**
     * @Then /^I should see (\d+) rows in the table$/
     */
    public function iShouldSeeRowsInTheTable($rows)
    {
        $table = $this->getPage()->find('css', '.main-content table');
        assertNotNull($table, 'Cannot find a table!');

        assertCount(intval($rows), $table->findAll('css', 'tbody tr'));
    }

    /**
     * @BeforeScenario
     */
    public function clearData()
    {
        self::$app['user_repository']->emptyTable();
        self::$app['product_repository']->emptyTable();
    }

    /**
     * @Given /^I author (\d+) products$/
     */
    public function iAuthorProducts($num)
    {
        for ($i = 0; $i < $num; $i++) {
            $product = self::$app['product_repository']->createProduct(
                'Sickle-shaped Claw'.$num,
                9.99+$i
            );

            $product->author = $this->currentUser;
            self::$app['product_repository']->update($product);
        }
    }

    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then /^break$/
     */
    public function iPutABreakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while (fgets(STDIN, 1024) == '') {}
        fwrite(STDOUT, "\033[u");

        return;
    }

    /**
     * @Given /^I wait for the dialog to appear$/
     */
    public function iWaitForTheDialogToAppear()
    {
        $this->getSession()->wait(
            5000,
            "jQuery('.modal').is(':visible');"
        );
    }

    /**
     * @Given /^the following products exist:$/
     */
    public function theFollowingProductsExist(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $product = self::$app['product_repository']->createProduct(
                $row['name'],
                15.99
            );

            if ($row['is published'] == 'yes') {
                $product->isPublished = true;
            } else {
                $product->isPublished = false;
            }

            self::$app['product_repository']->update($product);
        }
    }
}
