<?php

use AppBundle\Entity\Product;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

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
     * @BeforeScenario
     */
    public function clearData()
    {
        $purger = new ORMPurger($this->getContainer()->get('doctrine')->getManager());
        $purger->purge();
    }

    /**
     * @Given there is an admin user :username with password :password
     */
    public function thereIsAUserWithPassword($username, $password)
    {
        $user = new \AppBundle\Entity\User();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles(array('ROLE_ADMIN'));

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * @When I fill in the search box with :term
     */
    public function iFillInTheSearchBoxWith($term)
    {
        $searchBox = $this->getPage()
            ->find('css', 'input[name="searchTerm"]');

        assertNotNull($searchBox, 'Could not find the search box!');

        $searchBox->setValue($term);
    }

    /**
     * @When I press the search button
     */
    public function iPressTheSearchButton()
    {
        $button = $this->getPage()
            ->find('css', '#search_submit');

        assertNotNull($button, 'Could not find the search button!');

        $button->press();
    }

    /**
     * @Given there are :count products
     */
    public function thereAreProducts($count)
    {
        for ($i = 0; $i < $count; $i++) {
            $product = new Product();
            $product->setName('Product '.$i);
            $product->setPrice(rand(10, 1000));
            $product->setDescription('lorem');

            $this->getEntityManager()->persist($product);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @When I click :linkName
     */
    public function iClick($linkName)
    {
        $this->getPage()->clickLink($linkName);
    }

    /**
     * @Then I should see :count products
     */
    public function iShouldSeeProducts($count)
    {
        $table = $this->getPage()->find('css', 'table.table');
        assertNotNull($table, 'Cannot find a table!');

        assertCount(intval($count), $table->findAll('css', 'tbody tr'));
    }

    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin()
    {
        $this->thereIsAUserWithPassword('admin', 'admin');

        $this->getSession()->visit($this->locatePath('/login'));
        $this->getPage()->fillField('Username', 'admin');
        $this->getPage()->fillField('Password', 'admin');
        $this->getPage()->pressButton('Login');
    }

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    private function getPage()
    {
        return $this->getSession()->getPage();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
