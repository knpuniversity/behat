# Behat Loves Mink (Free Definitions from MinkExtension)

Behat parses scenarios and Mink is really good at browsing the web.
If we combine their powers, we could start having steps that look
a lot like what we have in `search.feature`. 

[[[ code('7f4a2e094e') ]]]

For the `Background` step, we *now* know we could create a matching
definition in `FeatureContext` and easily use Mink's session object
to actually go to that URL. But earlier when we ran this scenario,
it worked... so there must already be something tie'ing Behat and
Mink together.

Let's see what's happening.

## Free Behat Steps

First, in `FeatureContext` I had you extend `MinkContext`. Remove that now:

[[[ code('a7bbe58032') ]]]

When we run Behat, it needs to know *all* of the step definition language that's
available. You can see that list by passing a `-dl` to the Behat command:

```bash
php vendor/bin/behat -dl
```

This shows the four `ls` definitions we built. So, Behat opens the `FeatureContext` class,
parses out all of the `@Given`, `@When` and `@Then` annotations, and prints a final
list here for our enjoyment.

When we add more step definitions, this list grows. And if we use something that
*isn't* here yet, Behat very politely prints out the function for us in the terminal.

In `behat.yml` we added this `MinkExtension` configuration:

[[[ code('d2a6927dea') ]]]

This library ties Behat and Mink together and gives us two cool things. First, it
lets us access the Mink Session object inside of `FeatureContext`. We'll see that
soon.

For the second thing, add a new config called `suites:` and a key under that
called `default:` with a `contexts:` key. We'll talk about `suites` later. Under
`contexts`, pass `FeatureContext` *and*  `Behat\MinkExtension\Context\MinkContext`:

[[[ code('04fcda90bc') ]]]

Now, Behat will look inside `FeatureContext` *and* `MinkContext` for those definition
annotations.

Let's see what that gives us: run behat with the `-dl` option again:

```bash
php vendor/bin/behat -dl
```

Boom! Now we see a *huge* list! These include definitions for all common web actions, like
`When I go to` or `When I fill in "field" with "value"`. This includes the stuff we're
using inside of `search.feature`:

[[[ code('52d557550a') ]]]

So *that's* why that scenario already worked.

Let's take a look at where these come from. I'll use `shift+shift` and search for `MinkContext`:

[[[ code('dada600c08') ]]]

This looks just like our `FeatureContext`, but has a bunch of goodies already filled in.

So, why did I use this exact language inside of my scenario originally? Because, I'm lazy,
and I knew if I followed the language here, I'd get all this functionality for free. And
I'm from the midwest in the US: we love free things.

I'll take off the `@javascript` line:

[[[ code('5af4d56df5') ]]]

Since we don't need JavaScript, and now we should be able to run our search feature. Perfect!
