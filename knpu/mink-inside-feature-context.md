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

You know the drill, it's now time to try running our scenario. And we are given two functions that we
need to fill in now. 
