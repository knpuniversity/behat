Gherkin
=======

Writing Features (Defining Value)
---------------------------------

Gherkin is the language used to describe a feature and the scenarios that define
its behavior. It originally came from Cucumber, the Ruby-equivalent
of Behat and is just meant to be a natural, but structured feature story.

The feature template should look familiar: it consists of four lines that
define the business value and "user role":

.. code-block:: gherkin

    Feature: {custom title}
      In order to {benefit/value of the feature}
      As a {user/role who will benefit from this feature}
      I need to {short feature description}

The first line starts with ``Feature``, followed by a short title. 
This line should quickly highlight the purpose of this feature,
but otherwise isn't too important.

The next two lines, however, are *very* important. First, the ``In order to``
line defines the *value*. Why should we build this feature?
Why is it important? Will it bring us more visitors or keep those visitors
safe from dinosaur attack? The next line - starting with ``As a`` defines *who* 
will benefit from this value. Is it the admin user? Our normal web user? A defenseless park guest? 
If you have a hard time writing these first two lines, it's possible that this feature
just isn't a good idea. After all, if we're going to spend time and money
building something, shouldn't it have some value for a specific person?

Finally, the last line - starting with ``I need to`` - is a short description
of the types of actions the user will be able to take once this feature is
complete.

Since an example is worth a thousand words, let's look at a few. Pretend that a
big client idea has just been given to you, and it's your job to break it
into smaller pieces. From the 4-step process in the last chapter, our first
step is to **Define** the business value. In other words, create the four-line 
feature for each big part of the idea.

Suppose you hear "The site needs to be readable in French". The feature might
look like this:

.. code-block:: gherkin

    Feature: i18n
      In order to read the news in French
      As a French user
      I need to be able to switch locale

The value of the feature is clear: to be able to read news in French. The
user that benefits from the feature is any French user. The last line details
the types of things the user needs to do to get this business value. The
whole feature description is simple - we'll add more detail in the next step.

Imagine also that the same site needs a news admin panel:

.. code-block:: gherkin

    Feature: News admin panel
      In order to maintain a list of news
      As a site administrator
      I need to be able to add/edit/delete news

For the "value", we could say "In order to edit news". But is editing news
actually the true value? Instead, let's write "In order to maintain a list
of news". The user who's benefiting is our "site administrator". This makes
more sense - ultimately our site administrators want to be able to maintain
the list of news that shows up on the site. This is the true *business* value
of the feature - the web interface we'll build is just the tool to do that.

Let's do one more example. This time, imagine that park security wants to
control park fences from a mobile app, while vacationing thousands of miles away.

.. code-block:: gherkin

    Feature: Remote fence control API
      In order to control fence security from anywhere
      As an API user
      I need to be able to POST JSON instructions that turn fences on/off

The person benefiting in this case is our API user. This highlights another
reason why the "user" or "role" is so important: every line in a feature
is written from this person's point of view and using the technical level
of that person. This is *really* important, so I'll say it again. The entire
feature file is written from the first person point of view of the user
or role and should use language that's *only* as technical as that user understands.
In this example, an API user understands the meaning of "JSON instructions".
But if our role were "a park guest", we would avoid technical language
like this. When we start writing scenarios, this means that you should never
include CSS selectors: you understand what a CSS selector means, but your
generic "web user" definitely does not.

The reason behind this is simple. The *only* reason we're spending money to
build the feature is to benefit this one user type. If we can't even explain
the feature using their language, then our feature is either too technical
for that user, has no business value, or actually benefits some other user.
It's also helpful to imagine that this user is actually requesting the feature
from you, using their own language. In the real world, keeping the language
simple also means that you can write features and then send them back to
the client for approval.

For fun, let's look at a bad example of a feature. Suppose we've decided
to put delicious humans in front of dinosaurs to entertain them while in captivity:

.. code-block:: gherkin

    Feature: Delicious humans
      In order to be entertained
      As a dinosaur
      I need to be able to watch delicious humans pass by me all day

I love this example, because it sounds like something a big group of managers
might come up with. The problem is that "seeing delicious humans all day" 
probably does not actually entertain dinosaurs. If you think that you're
building this feature for their benefit, you're fooling yourself. This might
very well be a good feature, but the business value is that the company will
make money from park tickets, and the person benefiting from that is definitely not
your dinosaur.

Prioritizing
------------

Now that we've broken the big idea down into 3 features, we can prioritize
which we should work on first. And since we've focused on business value,
this is easy: just choose the feature that has the most. Alternatively, if
you need to make your admin users happy immediately, you might choose features
that benefit those users. We'll start with the news admin panel.

Prioritizing might not be something you normally do, but now it's easy. You
can make sure you repair the T-Rex fence before you send your first group
of visitors into the park. 

Writing Scenarios
-----------------

Once you've chosen a feature, it's time to write scenarios that describe
each part of it. As we saw earlier, each scenario follows a very
specific pattern. Start by giving it a name.

.. code-block:: gherkin

    Feature: News admin panel
    # ...
    
      Scenario: List available news

The body of a scenario is made up of three different parts: ``Given``, ``When``
and ``Then``. The first is ``Given``, which describes the initial state of
the system for the scenario. This is the *only* place where you can describe
things that the user can't do. In this case, the "site administrator" can't
magically put 5 news entries in the database, but that's ok. To have more
than one ``Given`` statement, start the next line with ``And``.

The second part of each scenario is ``When``, which describes the actual action
that this user is taking.

Finally, ``Then`` is used to describe what our user can see at the end of
the scenario.

.. code-block:: gherkin

    Feature: News admin panel
    # ...
    
      Scenario: List available news
        Given there are 5 news articles
        And I am on "/admin"
        When I click "News"
        Then I should see 5 news items

The exact language you use in your scenarios is up to you - just make sure
to follow the ``Given``, ``When``, ``Then`` format. Each line in the scenario
is called a "step", and should plainly describe what the user is doing and
seeing.

.. code-block:: gherkin

    Feature: News admin panel
    # ...

      Scenario: List available news
        # ...

      Scenario: Add a new news entry
        Given I am on "/admin/news"
        When I click "New entry"
        And I fill in "Title" with "Alan Grant does not endorse the park!"
        And I press "Save"
        Then I should see "Your article has been saved"

.. note::

    Technically speaking, there is no difference between ``Given``, ``When``,
    ``Then`` or ``And`` - Behat will process these steps completely the same.

If we didn't go any further, we would at least have a standard way of describing
our features. Writing scenarios also makes you think through each feature
in more detail. When you're finished, you've got a blueprint for exactly
what you need to develop, written in language that your client can understand.

Next, we'll use Behat to execute each Scenario as a test.