Behat + Mink
============

Now that you're dangerous with Behat and an absolute expert in Mink, let's
put them together!

Behat has a plugin system called "extensions", and we'll install a ``MinkExtension``,
which makes Behat and Mink play together like best friends! On the
`MinkExtension Documentation Page`_, copy the ``composer.json`` entry, paste
it into your file, and then run ``php composer.phar update behat/mink-extension``
to download the new library.

One thing we haven't mentioned yet is Behat configuration. By default, it
assumes a lot of things - like that our features live in a ``features/``
directory and that the ``FeatureContext`` class is found in ``features/bootstrap/``.
To configure this and a lot more, you can create a ``behat.yml`` file in
the root of your project or in a ``config/`` directory. We need this file
now because it's used to activate Behat extensions, like our ``MinkExtension``.
Copy the base configuration from the ``MinkExtension`` documents and also
add another ``selenium2`` line. This activates it and says that
we'll be using the ``goutte`` and ``selenium2`` drivers. For now, remove
the ``base_url`` line.

.. note::

    If you're using Behat in Symfony2, you should also install the Symfony2Extension
    and activate it here.

Accessing Mink and Built-in Definitions
---------------------------------------

Our goal is to get access to the Mink Session object from within our ``FeatureContext``
class. If we did that, we could write Behat steps like ``Given I go to "/contact"``
and very easily use Mink to command the browser accordingly. The ``MinkExtension``
helps us do this, but gives us a few options.

First, we can simply implement the ``MinkAwareInterface``. This forces us
to have two methods:: ``setMink`` and ``setMinkParameters``. The ``Mink``
object is a container that holds a pre-built ``Session``, and if we set
it an a private property, we could access the Session later. When Behat starts,
the ``MinkExtension`` looks to see if our ``FeatureContext`` class implements
this interface. If it does, it calls ``setMink`` and passes us exactly what
we need.

If you think that's too much work, it's your lucky day! The second option
is to extend the ``RawMinkContext`` class, which implements this interface
for you and gives you some nice shortcut methods like ``getSession``.

But wait there's more! Before we look at the third option, go to the command
line and ask Behat to print out the current list of all built-in definitions:

.. code-block:: bash

    php bin/behat -dl

Not surprisingly, it prints out the few custom definitions that we wrote 
earlier to test the ``ls`` command, but nothing else. Finally, make your
``FeatureContext`` extend ``MinkContext``. This has all the niceness of
``RawMinkContext``, but with one big surprise. Print your definitions again
to see that we've suddenly inherited a long list of really useful statements
like ``Given I am on`` and ``When I fill in "label" with "value"``. We just
got a bunch of awesome free stuff.

So where did all this free stuff come from? The answer is in the ``MinkContext``
class that we just extended. If you open this up, you'll see all the regular
expressions and functions that fuel these. This is a great place to look
if you're writing a custom definition and want some help on exactly how
to accomplish something.

Adding Web Scenarios
--------------------

Let's copy in the first scenario that we wrote all the way back in chapter 1.
For now, use the full URL to Wikipedia. We'll fix this in a few minutes.
Just like before, the scenario passes without writing any custom step definitions,
and now it makes sense why: every step in our scenario matches up with one
of the regular expressions that we inherited.

Let's try another scenario. Suppose someone says to you:

>If you are on the main page and you fill in the search field with
>"Tyrannosaurus Bill" and press "search", then you should see "Search results"

Since Tyrannosaurus's name is Rex and not Bill, this scenario outlines what
happens when you search for an article that doesn't exist. This time, the
scenario is super-quick to write.

.. code-block:: gherkin

    Scenario: Searching for a page that does NOT exist
      Given I am on "http://en.wikipedia.org/wiki/Main_Page"
      When I fill in "search" with "Tyrannosaurus Bill"
      And I press "searchButton"
      Then I should see "Search results"

And once again, without any PHP code, our scenario passes.

Background and Scenario Outline
-------------------------------

Two scenarios and no code yet, we're doing great! Let's clean up a few things
by reducing duplication. First, use the Background strategy we learned earlier
to remove the identical ``Given`` step on each scenario.

.. code-block:: gherkin

    Feature: Search
      # ...

      Background:
        Given I am on "http://en.wikipedia.org/wiki/Main_Page"

      Scenario: Searching for a page that does exist
        When I fill in "search" with "Velociraptor"
        And I press "searchButton"
        Then I should see "an enlarged sickle-shaped claw"

      Scenario: Searching for a page that does NOT exist
        When I fill in "search" with "Tyrannosaurus Bill"
        And I press "searchButton"
        Then I should see "Search results"

.. _behat-scenario-outline:

Besides the ``Given`` part, the remainder of the scenarios are also very
similar. In these cases, we can leverage something called "Scenario Outlines".
This Behat shortcut basically lets you replace any part of a scenario with
a variable. If we replace the search term and the expectation with variables,
then we can use a table to collapse our two scenarios into a single scenario
outline. As an extra bonus, some editors will even clean up your tables for
you.

.. code-block:: gherkin

    Background:
      Given I am on "http://en.wikipedia.org/wiki/Main_Page"

    Scenario Outline: Searching for a specific page
      When I fill in "search" with "<search>"
      And I press "searchButton"
      Then I should see "<expectation>"

      Examples:
        | search             | expectation                      |
        | Velociraptor       | an enlarged sickle-shaped claw   |
        | Tyrannosaurus Bill | Search results                   |

When we execute the feature, it looks a little different, but passes
just like before. Use "Scenario Outlines" whenever you want to test a number
of similar user interactions and outcomes.

behat.yml: base_url, Parameters, and Profiles
---------------------------------------------

The domain is not part of the behavior that we're describing or testing and
could change depending on if we're testing locally or on a staging server.
In other words, let's get it out of our feature file!

.. code-block:: gherkin

    Background:
      Given I am on "/wiki/Main_Page"

The secret to doing this is in the ``behat.yml`` file under a key called
``base_url``. By putting the domain here, it will be automatically prefixed
to our URLs.

.. code-block:: yaml

    default:
      extensions:
        Behat\MinkExtension\Extension:
          goutte:    ~
          selenium2: ~
          base_url: http://en.wikipedia.org/

But this isn't magic, in fact this trick is done quite simply. First, any
value you put here is available in your ``FeatureContext`` class by calling
the ``getMinkParameter`` function::

    /** @BeforeScenario */
    public function beforeScenario()
    {
        var_dump($this->getMinkParameter('base_url'));
    }

Open up the ``MinkContext`` class again and notice that every reference to
a URL is first passed to a ``locatePath`` function. This function holds the
magic behind how the ``base_url`` works, and as you can see, it's actually
really simple. Whenever you refer to a URL in your ``FeatureContext``, just
remember to wrap the URL in this function. And if you want even more magic,
you can always override this function and add whatever magic you want.

If you're curious about what other MinkExtension options are available, check out
`its documentation`_. Alternatively, if you open a class called `Extension`_
inside the library, you'll find a large configuration tree that highlights
all of the possible values, including less-known capabilities like telling
Selenium2 all of your desired capabilities.

behat.yml Parameters and Profiles
---------------------------------

If you want to pass your own configuration into Behat, you can do that beneath
a different key: ``context`` and ``parameters``. In this spot, you can pass
whatever you want.

.. code-block:: yaml

    default:
      context:
        parameters:
          foo: bar
          important_things: [one, two, apple]
 
      extensions:
        # ...

The values are passed into the constructor of the ``FeatureContext`` class,
which you can store as properties and use later however you want::

    private $fooConfig;
 
    public function __construct(array $parameters)
    {
      $this->fooConfig = $parameters['foo'];
    }

To see what other meaningful options you can pass beneath ``context``, see
the ``behat.yml`` part of the documentation.

There's also another trick called profiles. We could use it to redefine the
``base_url`` value, for example.

.. code-block:: yaml

    default:
      extensions:
        Behat\MinkExtension\Extension:
          base_url: http://en.wikipedia.org/
          # ...

    fr:
      extensions:
        Behat\MinkExtension\Extension:
          base_url: http://fr.wikipedia.org/

.. tip::

    Every profile inherits the configuration from the ``default`` profile
    and then overrides it.

To execute a test with a certain profile, just use the ``p`` option.

.. code-block:: bash

    php bin/behat -p fr

The French Wikipedia is a bit different so the test fails, but you get the idea.

Implementation Details: don't include CSS Selectors!
----------------------------------------------------

We have one big problem still with our scenario: we're referring to both
the search box and the search button by their HTML name attribute. This is
a very bad practice, because this isn't something that our user can see,
it's an implementation detail that might change during development. These
invisible changes aren't behavior changes, so our feature shouldn't need
to change. Including CSS or other technical details is very common for beginners,
but a very bad practice. Fortunately, it's easy to fix!

Replace the two offending lines with natural language that the user might
actually say. Since there isn't any text on the button, a natural way to
express these are ``When I fill in the search box with "<search>"`` and
``And I press the search button``. These aren't built-in definitions, I'm
just inventing what sounds natural.

.. code-block:: gherkin

    # ...

    Scenario Outline: Searching for a specific page
      When I fill in the search box with "<search>"
      And I press the search button
      Then I should see "<expectation>"

      Examples:
        # ...

Execute Behat so that it will give us the code snippets we need to fill in.
In fact, by using the ``--append-snippets`` flag, Behat will append the code
blocks for us. Just when you thought we couldn't get lazier, another shortcut!

.. code-block:: bash

    php bin/behat features/search.feature --append-snippets

To fill in these methods, we have two options: let's do the hard way first.
The Mink Session is available by saying ``$this->getSession()`` and the page
via ``$this->getSession()->getPage()``. Since you'll need the page all the
time, let's go ahead and make a shortcut method to get it. I'm being careful
with my PHPDoc so that my IDE gives me autocomplete::

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    protected function getPage()
    {
        return $this->getSession()->getPage();
    }

Back to work! We can use CSS to find the search box by using the ``find``
method. Alternatively, we can use the ``named`` selector by taking advantage
of the ``findField`` shortcut method. Once we have the field, just call ``setValue``
on it::

    /**
     * @When /^I fill in the search box with "([^"]*)"$/
     */
    public function iFillInTheSearchBoxWith($searchTerm)
    {
        $ele = $this->getPage()->findField('search');
        $ele->setValue($searchTerm);
    }

For the button, we'll use the ``findButton`` method and then call ``press``
on it::

    /**
     * @Given /^I press the search button$/
     */
    public function iPressTheSearchButton()
    {
        $this->getPage()->findButton('searchButton')->press();
    }

Notice that with both of these, it's ok to include CSS selectors and other
technical details inside the ``FeatureContext`` class. We only want to hide
them from the Feature file, which should describe the features behavior at
the technical level of our user.

It works! One great thing about this is that we can use these new definitions
on all of our future scenarios: we only have to do this work once.

Metasteps
---------

As easy as that last step was for such a Mink expert, there's an even easier
solution: metasteps. Metasteps let us use Gherkin language right inside a
custom step definition. Get rid of all of that Mink code, create a new instance
of a ``When`` object, and re-use the same Gherkin step language we had earlier.
There are also ``Given`` and ``Then`` classes, but all three do the exact
same thing, so use whichever you want::

    // ...
    use Behat\Behat\Context\Step\Given;
    use Behat\Behat\Context\Step\When;
    use Behat\Behat\Context\Step\Then;
    
    // ...

    public function iFillInTheSearchBoxWith($searchTerm)
    {
        return new When(sprintf(
            'I fill in "search" with "%s"',
            $searchTerm
        ));
    }

    public function iPressTheSearchButton()
    {
        return new When('I press "searchButton"')
    }

Metasteps are a really simple way to take the technical stuff out of your
Feature without needing to write any real code.

But they're also very useful in another situation. Imagine for a second that
we need to login, so we write something like ``Given I am logged in``.

.. code-block:: gherkin

    Background:
      Given I am on "http://en.wikipedia.org/wiki/Main_Page"
      And I am logged in

In reality, logging requires several steps, but we don't want to repeat these
on each scenario: we don't care how you login, we only care that it happens.

In this case, metasteps become *very* useful because you can actually return
a whole array of steps that should be executed::

    /**
     * @Given /^I am logged in$/
     */
    public function iAmLoggedIn()
    {
        return array(
            new Given('I am on "login"'),
            new Given('I fill in "Username" with "Ryan"'),
            new Given('I fill in "Password" with "foobar"'),
            new Given('I press "Login"'),
        );
    }

By writing one simple step in a scenario, you can trigger a whole group of
actions to be taken. Use this often for your most commonly needed tasks.
And just like with the Gherkin scenarios, there is no difference between
using ``Given``, ``When`` or ``Then``: use whichever one sounds most natural
to you.

.. _`MinkExtension Documentation Page`: https://github.com/Behat/MinkExtension/blob/master/doc/index.rst
.. _`its documentation`: https://github.com/Behat/MinkExtension/blob/master/doc/index.rst#additional-parameters
.. _`Extension`: https://github.com/Behat/MinkExtension/blob/1.3/src/Behat/MinkExtension/Extension.php#L173