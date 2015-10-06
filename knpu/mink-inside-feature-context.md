# Mink Inside Feature Context

Earlier I said that the `searchTerm` here is the actual name attribute 
of this box and the `search_submit` is the id of this button here. Well,
listen up y'all, I'm about to tell you one of the most important things about
working with Behat. All of the built in definitions that we have, almost all of
them use the `namedSelector`. 

There are two ways to find things with Mink, through CSS selectors or with the
`namedSelector` which is where you find fields, buttons and links with the actual
text that you see on the frontend of your site. Down here, in the built in definition
for "I fill in "field" with "value", value is going to be the label of the field, not 
the id, name attribute, or CSS selector. It's the same thing with clicking links, which
here is under "I follow" the `<link>` will be the actual name of the link, if you put a
CSS selector in there it's not going to work. 

If I changed `search_submit` to be a CSS selector and run it, that's going to fail. 

The name selector does let you cheat a little bit, because in addition to the actual text
of a field, like the text inside of a button or the label for a field, it also includes 
the name attribute, which is why this works, and the id attribute as special cases. But,
very important, you should *not* use the name or id. In our example we had to because this
field here doesn't have a label so there's nothing for me to target. Same with this button,
it only has an icon no actual text. I can't really use the named selector properly here. 

The cardinal rule in Behat is that you should never use CSS selectors or other things like the
id or names inside of your scenarios. Why? Because the person who is benefiting from the feature
is a web user and we're writing this from their point of view. A web use won't understand what 
`searchTerm` or `search_submit` means.

We're in a situation where we can't use the built in selectors properly, the only way to do it is to
cheat. That's ok, we need to build our own custom definitions. I'll change this to:

    When I fill in the search box with "<term>"

If I can't target it with any real name then we'll just make a new natural sounding, human readable sentence.
It's being higlighted here because it's an undefined step reference, this is a new step that we'll have to
fill in. We'll have the same thing down here,

    And I press the search button 

You know the drill, it's time to try running our scenario. And we are given two functions that we
need to fill in now. 

For us this shouldn't be hard because we know how to use the Mink session and page objects to do work 
like this. The issue is, how do I get access to the Mink session object? If you gave this to me inside
of `FeatureContext` I'm dangerous...so....where is it? There's a couple ways to get it, but the easiest
is to change this base class here to extend `RawMinkContext` instead of `MinkContext`. Looking inside
of here we can see it gives us access to a bunch of functions, the most important being `getSession` and
`visitPath` which we'll look at a bit later.

What we need to do here is fill in the search box. I'll change `arg1` to be `term` and next we need to
find the search box. It's not ok to use CSS selectors inside of your scenario, but, it's perfectly fine
to use them inside of your `FeatureContext` step definitions. 

Back in our browser let's inspect the search box element. It doesn't have an id but it does have this 
`name=searchTerm`, so if nothing else we can find it via the `name=` attribute there. Let's find that
box! `$searchBox = $this->getSession()` which gets our session, and remember we want to drill down
into elements so we'll need to say `->getPage` off of that which represents the whole page. Then we'll
find it via CSS with `->find('CSS', '[name="searchTerm"]');`. Let's add in an `assertNotNull` in the
event that our search box isn't found for some reason. We'll fill that in with `$searchBox, 'The search box was not found'`.
Now that we have this individual element we can take action on it with all of the cool functions that
come with being an individual element. Like `attachFile`, `blur`, `check`, `click`, `doubleClick` and
one of them is `setValue`. So if it's a form field we can set the value to `$term`. 

This is a perfect step definition, you find an element and you do something with that element. Down here
for pressing the search button we can do the exact same thing. `$button = $this->getSession()`, `->getPage`,
`->find('css', '#search_submit');` and `assertNotNull($button, 'The search button could not be found')`. It's
always a good idea to code defensively. And here is a method called `press`, and that's it!

Let's give this scenario another try. It passes! Why is this better again? Because we don't have css inside of
here anymore which makes our scenarios less dependent on the markup on our site and it makes them easier to
understand. I can read this and understand the expected behavior, whereas before it was a little more confusing.

Last thing, we're going to want to get the page all the time so I'm going to create a `private function getPage()`
so we can call that instead of `$this->getSession()->getPage`. So `return $this->getSession()->getPage();` and
I'll put a little PHPDoc up here so next month we'll remember what this is. 

Now we can shorten things a little bit with `$this->getPage()` here and `$this->getPage()` here. 

Try that out in our terminal one more time. Perfect! Now we have access to Mink inside of our `FeatureContext`
and we know not to include CSS inside of our scenarios.

