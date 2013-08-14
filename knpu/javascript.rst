JavaScript
==========

We saw earlier how Mink can be used to execute tests in a headless browser
like Goutte or using a driver like Selenium2 that supports JavaScript. So
how can we do this inside Behat?

Let's start by adding another scenario. Suppose someone says:

>If you fill in the search field with "Tyran" and wait for the suggestion
>box to appear, then you will see "Tyrannosaurus" in it.

This tests the search autocomplete on Wikipedia, which obviously requires
JavaScript. But don't worry about that yet, first worry about writing the
scenario. Remember to use as many of our built-in definitions as possible.

.. code-block:: gherkin

    Scenario: Searching for a page with autocompletion
      When I fill in the search box with "Tyran"
      And I wait for the suggestions box to appear
      Then I should see "Tyrannosaurus"

Since we're about to use Selenium2, make sure you've started the Selenium2
server, which is the JAR file we downloaded :ref:`earlier<behat-download-selenium>`.

To run the scenario in JavaScript, just add a ``javascript`` tag above the
scenario.

.. code-block:: gherkin

    @javascript
    Scenario: Searching for a page with autocompletion
      # ...

When we run the test, the browser opens up, but our test doesn't
pass yet: we're missing one very important step definition.

Waiting for things to happen
----------------------------

The autocomplete drop-down doesn't show up instantaneously, which is why
we added the wait step to our scenario. But how can we wait for things with
Mink? The answer is, well, the ``wait`` function!

.. code-block:: php

    /**
     * @Given /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getSession()->wait(5000);
    }

If we run the test now, it passes, but it waits a whole five seconds to be
sure the auto-complete opens. This is far from ideal - if you add a few of
these waits, your tests are going to slow *way* down.

Instead, we can add a second argument to ``wait``, which is some JavaScript
that's run every 100 milliseconds. As soon as the JavaScript expression returns
true, Mink will stop waiting. And if it takes longer than 5 seconds, it will
finally give up with an error::

    /**
     * @Given /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getSession()->wait(
            5000,
            "$('.suggestions-results').children().length > 0"
        );
    }

Since this JavaScript is run on your page, you can use whatever JavaScript
libraries you have available. Wikipedia uses jQuery, so we can take advantage
of it.