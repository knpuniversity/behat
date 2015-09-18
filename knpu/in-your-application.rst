You bet your sweet App
======================

You now know all the important things about Behat and Mink and how they work
together. In this chapter, we'll talk about some specific challenges that
you'll likely face when using Behat in your application, like how to handle
data fixtures.

I've created a miniature application using Silex for us to play with. Don't
worry if you're not using Silex - we'll keep the instructions here as generic,
but useful as possible. You'll still have a bit of homework to do for your
specific application, but we'll guide you.

.. note::

    The Silex application we're using here is available in this screencast's
    code download. (FTW!)

Bootstrapping your Application in FeatureContext
------------------------------------------------

To test our application, we'll make real HTTP requests as an outsider: surfing
to pages, filling out forms, and checking how everything looks afterwards.
To do this, we don't need access to our project's code, nor do we even need
to be on the same server!

But when you are on the same server, it can be *very* useful to bootstrap
your application inside ``FeatureContext``. This allows you access to all
the functions and tools that you use to do things like connect to the database,
clear cache, or anything else. As we'll talk about in a second, having access
to your normal tools might make clearing and preparing data pretty easy.

Exactly *how* you do this will be different for every application and framework,
but it will follow the same pattern. Usually it involves requiring some core
bootstrap file and possibly calling some sort of initialize function.

.. tip::

    If you're using Symfony2, install the `Symfony2Extension`_ to bootstrap
    Symfony for you and give you access to the Kernel. The container
    is then available as ``$kernel->getContainer()``.

Since we only need to bootstrap our application once per test suite, we can
take advantage of the ``beforeSuite`` hook. Your setup - and even the hook
to use - may be different::

    private static $app;

    /**
     * @BeforeSuite
     */
    static public function bootstrapSilex()
    {
        if (!self::$app) {
            self::$app = require __DIR__.'/../../app/bootstrap.php';
        }

        return self::$app;
    }

For Silex, the ``$app`` object is the key to any functionality we may need.
In your app, you may need a different variable, or you might just need to
statically call a function that magically gives you access to all of your
classes and functions. The best way to find out how your code should look
is to google to see if others have bootstrapped your framework for testing,
or to look at the code in your front controller.

In the next sections, you'll see how having access to the built-in functions

Preparing Data
--------------

One of the most complex issues with testing is dealing with
and controlling the data you test with. If you're testing against an external
server, you may not be able to control your data at all. In this case, you'll
have to be a bit more careful and clever about how you write your tests.
Fortunately, since we're on the same server as our application, we'll have
full control over our data.

Let's start with a scenario I already prepared, which tests the list page of a
product admin section.

.. code-block:: gherkin

    Feature: Product admin
      In order to manage the content on my site
      As an admin
      I need to be able to add, edit and delete products
      
      Scenario: Seeing a list of existing products
        Given I am logged in as an admin
        And there are 5 products
        And I am on "/admin"
        When I follow "Products"
        Then I should see 5 rows in the table

In order for this scenario to work, we need to guarantee that there is an
admin user in the database and we need the ability to add 5 products. If
you've bootstrapped your application in ``FeatureContext``, this should be
possible. Execute ``bin/behat`` so that the missing definitions are
added to our ``FeatureContext``.

I'll fill in the code that my system needs to create my admin user. Notice
that we didn't say ``Given there is an admin user called "admin"`` in our
scenario. We don't really care about that detail, so we skip it and just make
sure the user exists inside this definition. Next, use metasteps to actually
login with this user. The exact wording will vary for your app::

    /**
     * @Given /^I am logged in as an admin$/
     */
    public function iAmLoggedInAsAnAdmin()
    {
        self::$app['user_repository']->createAdminUser(
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

I'll also add the code to insert 5 products. Notice that we're not testing
the actual creation of products in this scenario. We may do that later in
another scenario, but for now we want to insert them as quickly as possible
to test that the user sees them::

    /**
     * @Given /^there are (\d+) products$/
     */
    public function thereAreProducts($num)
    {
        for ($i = 0; $i < $num; $i++) {
            self::$app['product_repository']->createProduct(
                'Sickle-shaped Claw'.$i,
                9.99+$i
            );
        }
    }

Finally, write some custom Mink code for the final step. This step is purposefully
generic, so that we can re-use it on other pages. Let's use the ``find`` method
to find a single table, then use it again to return an array of all of the
``tr`` elements. Use the PHPUnit assert functions to make sure we have the
right number of rows::

    /**
     * @Then /^I should see (\d+) rows in the table$/
     */
    public function iShouldSeeRowsInTheTable($rows)
    {
        $table = $this->getPage()->find('css', '.main-content table');
        assertNotNull($table, 'Cannot find a table!');

        assertCount(intval($rows), $table->findAll('css', 'tbody tr'));
    }

Great! Execute Behat. It passes! There's a lot going on behind the scenes,
but the actual scenario is described with simple language.

Use Data Fixtures?
------------------

Another approach to loading data is using some sort of fixture, which inserts
a whole set of default data. This may sound easy, but it's not a great approach
for two reasons. First, loading the extra data makes your tests run a bit
slower. Second - and more importantly - loading a set of data can make your
scenarios less readable. In our scenario, we're being very specific about
what data we have. If we loaded fixtures beforehand that contained 5 products,
we might remove the ``And there are 5 products`` line. But now our scenario
is a bit confusing - why are we expecting 5 products? Where did these products
come from?

For those reasons, do your best to avoid loading fixtures. The one exception
might be if you have lookup tables that contain data that never changes,
and is important to your application. An example might be a table called
``product_status``, with entries like ``Published``, ``Draft``, ``Archived``.
Since this data is static, it just needs to be there, so loading it before
your tests is probably a good idea.

Cleaning out Data
-----------------

Run the test again. This time, it fails spectacularly. When we try to insert
a second user with the same username, a unique constraint in the database
fails. As important as it is to add the data you need in a scenario, you
also need to clean out data. At the beginning of each scenario, you should
be able to assume that there is no data in the database. This prevents us
from needing to say things like ``Given there are no users`` before saying
``Given I am logged in as an admin``. The fact that we need to empty the
user table before inserting a user is an implementation detail - not part
of the feature's description.

Exactly how you handle this depends on your application, but it almost always
involves another ``BeforeScenario`` hook. Create a new function called ``clearData``
and tag it with ``BeforeScenario``. In here, your goal is to empty the data
in the database. In reality, you can do this, or just empty the tables that
you know should be cleared before each test. For now, let's clear the ``user``
and ``product`` tables::

    /**
     * @BeforeScenario
     */
    public function clearData()
    {
        self::$app['user_repository']->emptyTable();
        self::$app['product_repository']->emptyTable();
    }

We're using ``BeforeScenario`` here because each scenario should be independent
of every other scenario. In other words, data built in one scenario shouldn't
be used in the next. By clearing out the data before each one,
we're helping to guarantee that independence.

Re-run the test to see that things pass once again. Regardless of how you
clear it, make sure to always think about what data you have and what you're 
adding so that you're testing against the exact data you want.

Using the Current User and other Objects
----------------------------------------

Sometimes you'll write a scenario where you refer to something that was just
created in the background. For example, what if we want the five new articles
to be authored by us, the admin user? Lets change our scenario to reflect
this:

.. code-block:: gherkin

    Scenario: Seeing a list of existing products
      Given I am logged in as an admin
      And I author 5 products
      # ...

Run behat to generate this new step definition. We already know how to create
products, but how can we set our user as the author? The trick is to set
the current user on a private property when we login::

    private $currentUser;
    
    // ...
    
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
            // ...
        );
    }

    /**
     * @Given /^I author (\d+) products$/
     */
    public function iAuthorProducts($num)
    {
        for ($i = 0; $i < $num; $i++) {
            $product = self::$app['product_repository']->createProduct(
                'Sickle-shaped Claw'.$i,
                9.99+$i
            );

            $product->author = $this->currentUser;
            self::$app['product_repository']->update($product);
        }
    }

Once we've done this, we can use it in this definition or any other in the
future. This is one of the pro tips to using Behat, and we saw it once before
during the ``ls`` scenarios. If you need access to something between steps,
just store it on a property. Scenarios should be completely independent of
each other, but the steps in a scenario can be totally *dependent*.

Customizing behat.yml on each Machine
-------------------------------------

Finally, let's cover one more obstacle to using Behat in your project. The
``behat.yml`` file holds the ``base_url`` configuration, and based on our
virtualhost configuration, this value may be different on your machine versus
my machine. But how can we commit this to our repository without everyone
needing to modify this file and try *not* to commit those changes?

There are a few ways to handle this, but one of them is with the ``imports``
configuration. In ``behat.yml``, import a separate file called ``behat.local.yml``
and then move the ``base_url`` into it.

.. code-block:: gherkin

    # behat.yml
    default:
      extensions:
        Behat\MinkExtension\Extension:
          goutte:    ~
          selenium2: ~

    imports:
      - behat.local.yml

.. code-block:: gherkin

    # behat.local.yml
    default:
      extensions:
        Behat\MinkExtension\Extension:
          base_url:  http://store.l

The point of this is that we'll commit ``behat.yml``, but add ``behat.local.yml``
to our ``.gitignore`` file. When someone sets up the project for the first
time, they'll just create this file and customize it however
they need.

To make this easier, copy this file to ``behat.local.yml.dist``.

.. code-block:: bash

    cp behat.local.yml behat.local.yml.dist

This new file has no functional purpose, but you can use it to create the local
file when you setup the project.

.. _Symfony2Extension: https://github.com/Behat/Symfony2Extension

