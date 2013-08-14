Introduction
============

Hey! Welcome to the tutorial that we're calling "All about the World of Behat". 
Our goal is simple: to understand the Behavior-Driven Development
philosophy and master two tools - Behat & Mink - that will make you a functional-testing
legend.

.. tip::

    Behat and Mink have been developed by the open source community and are
    led by our friend and yours: Konstantin Kudryashov - aka @everzet: http://twitter.com/everzet.
    He was a huge help in the creation of this course!

Why test your application? Well imagine you are running Jurassic Park, you
need to know that adding the new Pterodactyl exhibit won't turn off the electric
fence around the velociraptor pen. Don't have any tests? Good luck - they know
how to open doors.

Getting good at practicing behavior-driven development - or BDD - means more
than learning a new tool, it will change your entire development process
for the better. Imagine a world where communication on your team is perfect,
you always deliver *exactly* what your client wanted, electricity on the velociraptor
fence never goes down and chocolate ice cream is free. Ok, we can't promise
all of that, but we'll see how BDD makes developing fun all over again.

In this first part, I'm going to show you how to install everything you
need and write your first feature and scenarios. After that, we'll back up
to learn more about each important part: scenarios & features, step definitions,
advanced Mink, and other really important topics.

Installation
------------

Before we begin, let's install a few libraries. Start by creating a new directory
and adding a ``composer.json`` file.

.. note::

    If you're testing an existing application, do all of this inside your
    project directory.

Composer is a tool that helps download external libraries into your project.
If you're not familiar with it, it's totally ok! We have a `free course`_ that
explains it.

.. note::

    Watch "The Wonderful World of Composer Tutorial" at http://bit.ly/KnpU-Composer

We'll be downloading Behat, Mink, and a few other related libraries into our project.
To make this easy, we've prepared a `gist`_ with exactly what you need to add
to your ``composer.json``.

.. code-block:: json

    {
        "require": {
            "behat/mink": "1.4@stable",
            "behat/mink-goutte-driver": "*",
            "behat/mink-selenium2-driver": "*",
            "behat/behat": "2.4@stable",
            "behat/mink-extension": "*"
        },
        "minimum-stability": "dev",
        "config": {
            "bin-dir": "bin/"
        }
    }

.. tip::

    If you're using Behat in a Symfony2 project, you'll also want to include
    the `Symfony2 Extension`_.

Next, `download Composer`_ by going to GetComposer.org and clicking download.
Copy one of the two code blocks, depending if you have ``curl`` installed,
and paste into the terminal. This downloads a standalone ``composer.phar``
executable.

.. tip::

    Remember, we talk a lot more about composer in "The Wonderful World of
    Composer Tutorial" at http://bit.ly/KnpU-Composer

Next, tell Composer to download the libraries we need by running ``php composer.phar install``

.. code-block:: bash

    $ php composer.phar install --prefer-dist

We'll fast-forward through this thrilling process as it downloads each library
and places it into a new `vendor/` directory. When it's finished, you'll
also notice a new ``bin/`` directory with a ``behat`` file in it. This is
the Behat executable, and you'll use it to run your tests and get debug information.

Next, create a ``behat.yml`` file at the root of the project. When Behat
runs, it looks for a ``behat.yml`` file, which it uses for its configuration.

.. tip::

    For more information about the ``behat.yml`` configuration file,
    see `Configuration - behat.yml`.

We'll use it to activate `MinkExtension`_, which is like a plugin for Behat.
Also, we're going to test Wikipedia, so use it as the ``base_url``. If you're
testing your application, use its local base URL instead. Hopefully you'll
join us for the rest of this course, where we'll go into greater detail.

.. code-block:: yaml

    default:
      extensions:
        Behat\MinkExtension\Extension:
          goutte:    ~
          selenium2: ~
          base_url: http://en.wikipedia.org/

.. note::

    If you're using Behat with Symfony2, you should also activate the
    `Symfony2 Extension` that you added to ``composer.json``:
    
    .. code-block:: yaml

        default:
          extensions:
            # ... the MinkExtension code
            Behat\Symfony2Extension\Extension: ~

To get the project ready to use Behat, run ``php bin/behat --init``. This
creates a ``features/`` directory and a ``bootstrap/FeatureContext.php``
file inside of it.

.. note::

    If you're using Behat in Symfony2, run the command for a specific bundle.
    A ``Features`` directory will be created in that bundle, with a similar
    structure. If the directory is created at the root of your project, delete
    it and double-check that you've activated the ``Symfony2Extension`` in
    the ``behat.yml`` file:
    
    .. code-block:: bash
    
        $ php bin/behat @EventBundle --init

Open this file and make it extend ``MinkContext`` instead of ``BehatContext``::

    // ...
    use Behat\MinkExtension\Context\MinkContext;

    class FeatureContext extends MinkContext
    {
        .. ///
    }

Later on, we'll learn more about Behat and Mink individually, and
the importance of the ``MinkContext`` class will make more sense.

Woo! With all that installing and configuring behind us, let's get to locking
down the raptor cage!

Writing Features and running tests
----------------------------------

The Behat and Mink libraries are most commonly used to test web applications.
You describe a feature in a human-readable syntax called Gherkin, then execute
these as tests. The best way to see this in action is to take your DeLorean
back to the past a few years and imagine that Jimmy Wales has asked you to build
Wikipedia.org. Yes, we know this site actually exists, but we're going to
*describe* its behavior and run some functional tests against it.

First, forget about tests. Our goal is to describe the feature. We're
going to describe the Wikipedia search, so create a ``search.feature`` file
in the ``features`` directory. The language in this file is called `Gherkin`_
and you start by describing the feature using a specific, four-line syntax.
This defines the business value of the feature, who will benefit from it,
and a short description. So, when John Hammond comes to you with a big idea,
your first goal should be to try to describe it using these four lines. Writing
good feature descriptions is really important, and we'll spend more time on
this later.

.. code-block:: gherkin

    Feature: Search
      In order to find a word definition
      As a website user
      I need to be able to search for a word

Each feature has many scenarios, which describe the specific behavior
of the feature. Each scenario has 3 sections. ``Given`` which details the starting
state of the system, ``When`` which includes the action the user takes, and
``Then`` which describes what the user sees after taking action. In this scenario,
we're searching for an exact article that matches.

.. code-block:: gherkin

    Feature:
      # ...

      Scenario: Search for a word that exists
        Given I am on "/wiki/Main_Page"
        When I fill in "search" with "Velociraptor"
        And I press "searchButton"
        Then I should see "an enlarged sickle-shaped claw"

Great! In a normal application, we'd now start developing the feature until
it it fits our description of its behavior. But since Wikipedia exists already,
we can see the behavior in action!

Writing Features and Scenarios is great, because it helps clarify how something
should work in human-readable language. But the real magic is that we can
run the scenario as a functional test!

To do this, run ``php bin/behat``. Behind the scenes, this reads the scenario
and actually uses a real browser to go to Wikipedia, fill in the field, and
click the button!

To see how this is possible, execute Behat, but pass a ``-dl`` option:

.. code-block:: bash

    $ php bin/behat --dl

Behat's job is to read each line in the scenario and execute some function
inside our ``FeatureContext`` class. Because we're using Mink, we inherit
a lot of common sentences. You can use these to write tests without writing
any PHP code. You can also invent your own sentence and then create a new
method in the ``FeatureContext`` class. We'll talk a lot more about this later.

Executing Tests that use JavaScript
-----------------------------------

Our first scenario ran in the background using a headless
browser called `Goutte`_. Goutte runs very fast, you know like a velociraptor,
but it doesn't support Javascript. This was ok because our Scenario doesn't
rely on any JavaScript functionality.
But what if it did? Can we test things that use JavaScript?

.. _behat-download-selenium:

Of course! And with Behat & Mink, it's incredibly easy. First, `download Selenium Server`_,
which is just a jar file that can live anywhere on your computer. Start
Selenium at the command line by running ``java -jar`` followed by the filename.

.. code-block:: bash

    $ java -jar selenium-server-standalone-2.28.0.jar

Now for the magic. To make this one scenario execute using Selenium instead
of Goutte, add an ``@javascript`` tag above the scenario. Now just re-run
your Behat tests using the same command as before:

.. code-block:: bash

    $ php bin/behat

Magically, a browser opens up, surfs to Wikipedia, fills in the field and
presses the button. This is the most powerful feature of Mink: you can run
some tests using Goutte and other tests - that require JavaScript - in Selenium
simply by adding the ``@javascript`` tag.

Digging into Gherkin, Behat and Mink
------------------------------------

We now have a project using Behat & Mink, and our first feature file and
scenario. Using a bunch of built-in english sentences, we're able to write
tests without any work at all.

But to really get good, we need to dive deeper to find out how to write really
solid Feature files, how to create your own custom sentences, how to master Mink
to do really complex Browser tasks, and much more. So, keep going!

.. _`free course`: http://bit.ly/KnpU-Composer
.. _`gist`: http://bit.ly/behat-mink-composer
.. _`download Composer`: http://getcomposer.org/download/
.. _`Symfony2 Extension`: http://extensions.behat.org/symfony2/
.. _`Configuration - behat.yml`: http://docs.behat.org/guides/7.config.html
.. _`MinkExtension`: http://extensions.behat.org/mink/
.. _`Gherkin`: http://docs.behat.org/guides/1.gherkin.html
.. _`Goutte`: https://github.com/fabpot/Goutte
.. _`download Selenium Server`: http://seleniumhq.org/download/