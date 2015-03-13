The fun stuff Chapter
=====================

You're now a Behat and Mink pro! In this last chapter, I want to mention a
few tricks about debugging, and a number of other important tips and features.

Debugging Failures
------------------

So what happens when a test fails and you don't know why? Let's make our
product list page throw a big error and re-run the test::

    throw new \Exception('Ah, ah, ah, you didn\'t say the magic word!');

It fails of course, but it's not really obvious why.

Whenever you have a failure that you need to debug, use a special built-in
step called "print last response". If you forget the wording for this step,
just re-un ``bin/behat -dl`` - you'll find it near the bottom. Place this
step just above the one that fails and re-run your test:

.. code-block:: gherkin

    Scenario: Seeing a list of existing products
      Given I am logged in as an admin
      And there are 5 products
      And I am on "/admin"
      When I follow "Products"
      And print last response
      # the "Then" below is failing right now
      Then I should see 5 rows in the table

It may be a bit long, but you'll now see the HTML response from the last
page, just before the error. A lot of times, you'll see an error. You may
even realize that you're not on the right page - the URL at the top of the
output helps you figure that out. You can also copy the URL and put it in
your browser, which may help you see the error yourself.

Subcontexts
-----------

To run this test in Selenium, just add the ``@javascript`` tag above the
scenario. In Selenium we can still use ``print last response`` to debug, but
there's a much better way.

First, let's talk about an idea called Subcontexts. Normally, Behat reads
the annotations from our ``FeatureContext`` and any classes we extend, like
``MinkContext``. But what if we found a really cool open source context
that we wanted to use? We can only extend one other class, so how could we
get the definitions from this new one?

The answer is via a subcontext. We won't show it here, but you can actually
separate your definitions into as many context classes as you want. By calling
``useContext`` in the constructor of your main ``FeatureContext``, Behat
will use all the definitions and hooks from that class as well::

    public function __construct()
    {
        $this->useContext('other_context', new OtherContext());
    }

And in fact, there *are* some open source context classes out there that
you can use. One really interesting one is in the `Behatch Contexts`_
library. We're not going to install this extension, but let's browse its
`source code`_ to see a few interesting context classes it has. One is the
``BrowserContext``, which has some extra browser-related steps. Another is
the ``TableContext``, which has nice steps for dealing with tables. My only
word of warning is that many of these steps require you to put CSS in your
scenario. Remember, this is a *bad* practice. I really like this extension,
but because of these CSS definitions, I usually use it as a reference, rather
than actually installing it.

Debugging in Selenium
---------------------

The most interesting one is the ``DebugContext``, which contains a very
cool step called ``I put a breakpoint``. Copy this into our ``FeatureContext``
file and change the text to simply read ``break``. Update your scenario to
use this new step instead of the ``print last response``:

.. code-block:: gherkin

    Scenario: Seeing a list of existing products
      Given I am logged in as an admin
      And there are 5 products
      And I am on "/admin"
      When I follow "Products"
      And break
      # the "Then" below is failing right now
      Then I should see 5 rows in the table

When we run the test, it opens up Selenium as expected. But when it hits
the new step, it pauses. At the command line, you can see that it's actually
waiting for us to hit "enter" before it continues. This is the best way to
see what is in your actual browser and even play around with things to figure
out why something is failing. When you're ready to keep going, just hit enter
and the test will finish. I *love* this debugging technique.

Waiting for AJAX
----------------

When you're testing in JavaScript, Selenium2 is pretty good about waiting
for your page to load before trying to continue and click on any elements.
But when you start to load things with AJAX, you might start having problems.

We talked about this earlier when testing on Wikipedia, but let's see it again.
On our test app, if you press "New Product", an AJAX call is made in the
background, which causes a slight delay before the window opens. To see how
this is a problem, let's write a scenario that clicks this link and creates
a new product:

.. code-block:: gherkin

    @javascript
    Scenario: Add a new product via the dialog
      Given I am logged in as an admin
      And I am on "/products"
      When I follow "New Product"
      And I fill in "Name" with "New Article"
      And I fill in "Price" with "5.99"
      And I press "Save"
      Then I should see "Product created"

The scenario is simple, but when we run it, it fails! The "New Product" link
is clicked, but since Selenium doesn't see the "Title" field immediately,
it fails.

When you have these types of issues, you'll need to add a wait step. In this
case, we need to wait for the dialog box to appear, so let's just say that
in our scenario:

.. code-block:: gherkin

    @javascript
    Scenario: Add a new product via the dialog
      # ...
      When I follow "Create Product"
      And I wait for the dialog to appear
      And I fill in "Title" with "New Product"
      And I fill in "Body" with "Lorem Ipsum"
      # ...

Execute Behat so that it prints out the new definition code. Remember that
waiting is done with the ``wait`` function, but that we only want to wait
until the needed action happens. In our case we can find the Twitter Dialog
element and test to see if it is visible::

    /**
     * Wait for the twitter bootstrap dialog to appear
     *
     * @Given /^I wait for the dialog to appear$/
     */
    public function iWaitForTheDialogToAppear()
    {
        $this->getSession()->wait(
            5000,
            "$('.modal').is(':visible');"
        );
    }

This will wait for 5 seconds or until the modal becomes visible. Try the test
again. Egads It works! Using waits is critical to testing with JavaScript, but it's
also really important that you wait for specific things to happen, not static
lengths of time. If you're using consistent loading screens and dialogs,
then you should be able to write and re-use just a few wait steps.

The TableNode syntax: inserting a bunch of things at once
---------------------------------------------------------

Let's change our first scenario to be just a little more interesting. Right
now, we're inserting 5 products and checking for 5 products. That's a great
scenario, but we might also choose to insert some specific products, and
then check for them directly. I'm also going to change the behavior of the
application to only show published products on the list page:

.. code-block:: gherkin

    Scenario: Seeing a list of existing products
      Given I am logged in as an admin
      And there are the following products:
        | title                          | is published |
        | The T-Rex has escaped          | yes          |
        | They can open doors...         | yes          |
        | When Dinosaurs ruled the Earth | no           |
      And I am on "/admin"
      When I follow "Products"
      Then I should see 2 rows in the table 
      And I should see "The T-Rex has escaped"
      And I should not see "When Dinosaurs ruled the Earth"

You can already see how this scenario is now much more useful: we're not
only testing that the list page works, we're testing that it shows the products
it should and that it doesn't show un-published products.

The table syntax is the key here, any time you end a step with a colon, you'll
create a table of data that you want to pass into your function. When we
execute Behat, we'll see that the function it generates is passed a special
``TableNode`` object, which has all the data from that table.

With this object, we're dangerous! Using the ``getHash`` function, we can
iterate over each row to create the products we need. One important thing
to notice is that we try to keep the language in the table as natural as
possible, using "is published" and "yes" or "no" instead of "true" or "false"::

    /**
     * @Given /^there are the following products:$/
     */
    public function thereAreTheFollowingProducts(TableNode $table)
    {
        foreach ($table->getHash() as $productData) {
            $product = self::$app['product_repository']->createProduct(
                $productData['title'],
                15.99
            );

            if ($productData['is _published']) {
                $product->isPublished = true;
            }

            self::$app['product_repository']->update($product);
        }
    }

Run the test. It still works! We're really getting good at this!

You'll also see this table syntax in one important built-in definition:
``I fill in the following``. This can be used when you need to fill in a lot
of fields at once, and we can use it in our product creation scenario:

.. code-block:: gherkin

    @javascript
    Scenario: Add a new product via the dialog
      # ...
      When I follow "Create Product"
      And I wait for the dialog to appear
      And I fill in the following:
        | Title | New Product |
        | Body  | Lorem Ipsum |
      # ...

This is a great way to fill in big forms, while keeping our scenario clean.
There's also a similar syntax whenever you need to reference multi-line text. 
We won't talk about it here, but it's pretty easy to use.

Command-line Options: Running just one Scenario
-----------------------------------------------

The last thing I want to talk about is the many options you have when executing
behat. For example, what if we only want to execute one scenario? Executing
a single feature can be done easily by referencing only the filename that
you want to run:

.. code-block:: bash

    ./bin/behat features/product.feature

.. tip::

    If you're using Symfony2 and the Symfony2Extension, use the syntax
    ``./bin/beat @SomeBundle/product.feature``.

To execute only a single scenario, just find the line number where the scenario
starts, and add that to the end of the command:

.. code-block:: bash

    ./bin/behat features/product.feature:6

This is really awesome when debugging a failing scenario.

The ``behat`` executable has a lot of other useful options as well, which
you can see by adding ``--help`` after the command. If you're using ``behat``
on a continuous integration server, you may pass a ``--format=junit`` option
so that it outputs the JUnit XML format:

.. code-block:: bash

    ./bin/behat --format=junit --out=build/

Another useful option is ``--tags``. We've seen how you can tag a scenario
with ``@javascript`` to execute that test with Selenium. You can also invent
whatever tags you want, as a way to organize your tests. Once you've done
this, you can execute all the scenarios for a specific tag, all the scenarios
except those with a tag, or any other logical combination you can think of:

.. code-block:: bash

    ./bin/behat --tags=list

.. _`Behatch Contexts`: https://github.com/Behatch/contexts
.. _source code: https://github.com/Behatch/contexts