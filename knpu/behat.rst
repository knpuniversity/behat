Behat
=====

Installing Behat
----------------

To really learn what Behat does, let's hop back in our DeLorean and pretend
that we're writing the UNIX ``ls`` command. I'll create a new directory with
a new ``composer.json`` file. This time, we'll install *only* Behat by following
`Behat's Quick Tour`_:

.. code-block:: json

    {
        "require": {
            "behat/behat": "2.4.*@stable"
        },
        "minimum-stability": "dev",
        "config": {
            "bin-dir": "bin/"
        }
    }

Just like before, initialize the project by running ``php bin/behat --init``.
This creates the same ``FeatureContext.php`` class as earlier. In this case,
things are simple enough that we don't need a ``behat.yml`` file.

Feature, Scenarios and Custom Step Definitions
----------------------------------------------

Our project is setup, so let's create our first feature file called ``ls.feature``.
Remember to focus on the business value of the ``ls`` command:

.. code-block:: gherkin

    Feature: ls
      In order to see the directory structure
      As a UNIX user
      I need to be able to list the current directory's contents

Next, let's write some scenarios! Suppose that Linus Torvalds said to us:

.. code-block:: text

    If you have two files in a directory, and you're running the command - you
    should see them listed.

Let's turn this into our first scenario. Remember that we're following the
``Given``, ``When``, ``Then`` format, but using natural language:

.. code-block:: gherkin

    Feature: ls
    # ...

      Scenario: List 2 files in a directory
        Given I have a file named "john"
        And I have a file named "hammond"
        When I run "ls"
        Then I should see "john" in the output
        And I should see "hammond" in the output

The goal of Behat is to let you execute your scenarios as tests. So let's
try it! But this time, instead of running and passing, Behat prints out some
methods and regular expressions. Copy these into your ``FeatureContext``
class::

    /**
     * @Given /^I have a file named "([^"]*)"$/
     */
    public function iHaveAFileNamed($argument1)
    {
        throw new PendingException();
    }
 
    /**
     * @When /^I run "([^"]*)"$/
     */
    public function iRun($argument1)
    {
        throw new PendingException();
    }
 
    /**
     * @Then /^I should see "([^"]*)" in the output$/
     */
    public function iShouldSeeInTheOutput($argument1)
    {
        throw new PendingException();
    }

Behat works by reading each step, or line, in your scenario and executing
a method in ``FeatureContext``, which is called a "step definition". This
is done by matching the step to the regular expressions above each method.
Behat was also smart enough to generate wildcards in the regex: the quoted
values are passed as arguments to the methods. This makes it easy to create
re-usable steps.

Our job now is to fill in the body of each method. To check the output in
the last method, we can create a new ``output`` property on the class and
store the output there. This is a common trick when you need information
between different steps. Finally, to sandbox our test, we'll create and move
into a ``test/`` directory::

    private $output;
 
    public function __construct()
    {
        // this actually creates 2 test directories inside of each other!
        // the reason is subtle, and we'll fix this soon
        mkdir('test');
        chdir('test');
    }
 
    /** @Given /^I have a file named "([^"]*)"$/ */
    public function iHaveAFileNamed($file)
    {
        touch($file);
    }
 
    /** @When /^I run "([^"]*)"$/ */
    public function iRun($command)
    {
        exec($command, $this->output);
    }
 
    /** @Then /^I should see "([^"]*)" in the output$/ */
    public function iShouldSeeInTheOutput($string)
    {
        if (array_search($string, $this->output) === false) {
            throw new \Exception(sprintf('Did not see "%s" in the output', $string));
        }
    }

When we run ``bin/behat`` again, it works! As each step is read, each method
is executed.

Hooks!
------

But when we run Behat again, it blows up. If we scroll up, it makes
sense. Each test creates a ``test/`` directory, but never cleans it up.
To fix this, create a new method in ``FeatureContext`` that reverses the
setup work::

    public function moveOutOfTestDir()
    {
        chdir('..');
        if (is_dir('test')) {
            system('rm -r '.realpath('test'));
        }
    }

Behat creates a new ``FeatureContext`` object for each scenario that it runs,
which means that the ``__construct`` method is run before every scenario.
To tell Behat to run our clean method *after* each scenario just add an ``AfterScenario``
annotation::

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

While we're at it, let's also move the setup code into a method that's tagged
with ``BeforeScenario``::

    /**
     * @BeforeScenario
     */
    public function moveIntoTestDir()
    {
        mkdir('test');
        chdir('test');
    }

.. tip::

    If you're wondering why we didn't just use ``__construct`` and ``__destruct``,
    the answer is that these methods behave slightly differently than tagging
    methods with ``@BeforeScenario`` and ``@AfterScenario``.

Run Behat twice more to let the new methods clean things up. Now our test
is passing perfectly every time.

Using PHPUnit assert functions
------------------------------

If you have PHPUnit installed, then you can uncomment out a few lines at
the top of your test to make life easier. Once you've done this, you have
access to a bunch of PHPUnit assert functions. We can use one of them, ``assertContains``
to make our test a bit nicer on the eyes::

    /**
     * @Then /^I should see "([^"]*)" in the output$/
     */
    public function iShouldSeeInTheOutput($string)
    {
        assertContains(
            $string,
            $this->output,
            sprintf('Did not see "%s" in the output', $string)
        );
    }

The Second Scenario
-------------------

We've written one scenario, so let's try another! This time Linus tells us:

.. code-block:: text

    If you have one file and one directory, and you run the
    command - you should see them both listed too.

Hopefully, writing scenarios is getting easy:

.. code-block:: gherkin

    Feature: ls
    # ...

      Scenario: List 2 files in a directory
      # ...

      Scenario: List 1 file and 1 directory
        Given I have a file named "john"
        And I have a dir named "ingen"
        When I run "ls"
        Then I should see "john" in the output
        And I should see "ingen" in the output

Just like before, run ``bin/behat``, copy in the missing step definition,
and implement it. And with almost no work, this new scenario passes!

Background
----------

We now have two working scenarios, but a little bit of duplication. Specifically,
each scenario starts with the same ``Given I have a file named "john"``. To
fix this, add a ``Background`` before both scenarios:

.. code-block:: gherkin

    Feature: ls
    # ...

      Background:
        Given I have a file named "john"

      Scenario: List 2 files in a directory
        And I have a file named "hammond"
        When I run "ls"
        Then I should see "john" in the output
        And I should see "hammond" in the output

      Scenario: List 1 file and 1 directory
        And I have a dir named "ingen"
        When I run "ls"
        Then I should see "john" in the output
        And I should see "ingen" in the output

Background is dead-simple, but really useful! When we re-run the test, each
line in the background is executed before each scenario. Our scenarios are
executed exactly like before, but without the duplication!

In fact, Behat has more cool tricks, including :ref:`scenario outlines<behat-scenario-outline>`,
more hooks like ``BeforeScenario``, a way to organize your scenarios called
tags, and much more. We'll see more of these powerful tricks a bit later.

.. _`Behat's Quick Tour`: http://docs.behat.org/quick_intro.html#method-1-composer
