# Mink Session inside FeatureContext

In `search.feature`, this `searchTerm` is the `name` attribute of the search box.
And `search_submit` is the `id` of its submit button. Well, listen up y'all,
I'm about to tell you one of the most important things about working with Behat:
Almost every built-in definitions finds elements using the "named" selector,
*not* CSS.

## Cardinal Rule: Avoid CSS in your Scenarios

For example, look at the definition for

> I fill in "field" with "value"

To use this, you should pass the *label* text to "field", *not* the `id`, `name`
attribute or CSS selector. Clicking a link is the same. That's done with the

> I follow "link"

Where the link must be the *text* of the link. If you pass a CSS selector, it's not
going to work. If I changed `search_submit` to be a CSS selector, it'll fail.
Believe me, I've tried it a bunch of times.

Got it? Ok: in reality, the named selector lets you cheat a little bit. In addition
to the true "text" of a field, it also searches for the name attribute and the id
attribute. That's why our scenario works.

But *please* *please* - don't use the name or id. In fact, The cardinal rule in
Behat is that you should never use CSS selectors or other technical things in
your scenario. Why? Because the person who is benefiting from the feature is
a web user, and we're writing this from their point of view. A web user doesn't
understand what `searchTerm` or `search_submit` means. That makes your scenario
less useful: it's technical jargon instead of behavior descriptions.

So why did we cheat? Well, the search field doesn't have a label and the button
doesn't have any text. I can't use the named selector to find these, *unless*
I cheat.

## Custom Mink Definition

Whenever you want to cheat or can only find something via CSS, there's a simple
solution: use new language and create a custom definition. Change the first
line to:

    When I fill in the search box with "<term>"

If I can't target it with real text, I'll just use some natural language.
PhpStorm higlights the line because we don't have a definition function matching
this text. For the second problem line, use

    And I press the search button 

You know the drill: it's time to run the scenario. It prints out the two functions
we need to fill in.

## Getting the Mink Session

Filling these in shouldn't be hard: we're pretty good with Mink. But, how can we access
the Mink Session? There's a couple ways to get it, but the easiest is to make
`FeatureContext` extend `RawMinkContext`. This gives us access to a bunch of functions:
the most important being `getSession()` and another callex `visitPath()` that we'll use
later.

On the first method, change `arg1` to `term`. Once you're inside of `FeatureContext`
it's *totally* ok to use CSS selectors to get your work done.

Back in the browser, inspect the search box element. It doesn't have an id
but it does have a name attribute - let's find it by that. Start with
`$searchBox = $this->getSession()->getPage()`. Then, to drill down via
CSS, add `->find('css', '[name="searchTerm"]');`. I'm going to add an `assertNotNull`
in case the search box isn't found for some reason. Fill that in with
`$searchBox, 'The search box was not found'`.

Now that we have the individual element, we can take action on it with one
of the cool functions that come with being an individual element, like
`attachFile`, `blur`, `check`, `click` and `doubleClick`. One of them is
`setValue()` that works for field. Set the value to `$term`. 

This is a perfect step definition: find an element and do something with it

To press the search button, we can do the exact same thing.
`$button = $this->getSession()->getPage()->find('css', '#search_submit');`.
And `assertNotNull($button, 'The search button could not be found')`. It's
always a good idea to code defensively. This time, use the `press()` method.

We're ready to run the scenario again. It passes!

That was more work, but it's a better solution. With no CSS inside of our
scenarios, they're less dependent on the markup on our site and this is a
heck of a lot easier to understand than before with the cryptic name and ids.

## Create a getPage Shortcut

To save time in the future, create a `private function getPage()` and
`return $this->getSession()->getPage();`. I'll put a little PHPDoc above this
so next month we'll remember what this is.

Now we can shorten both definition functions a bit with `$this->getPage()`.

Test the final scenarios out. Perfect! Now we have access to Mink inside of
`FeatureContext` *and* we know that including CSS inside of scenarios is
not the best way to make friends.
