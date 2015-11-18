# Tagging Scenarios in order to Load Fixtures

Remember *way* back in the beginning when we had search feature? Try running that
again:

```bash
./vendor/bin/behat features/web/search.feature
```

Huh, this one is failing now: it says that the text "Samsung Galaxy" was not found
anywhere on the page. Now that you're an expert, I hope you can spot the problem:
we're not adding this product at the beginning of the scenario. This worked originally
because the fixtures that come with the project have a "Samsung Galaxy" product.
But now that other tests have cleared the database, we're in trouble.

We *could* put some `Given` statements at the top to add the products. But there's
another way: load the project's fixtures automatically before the scenario. This
`LoadFixtures` class is responsible for putting in the Kindle and Samsung products.

I think that entering the data manually with the `Given` statements is the most readable
way to do things. But, if you do load the fixtures, here's the best way. First, I
*don't* want to load fixtures before every scenario. That would make my scenarios
run slower, even when I don't need that stuff.

## Tagging Scenarios

Instead, I need a way to *tag* scenarios to say "this one needs fixtures". Add
`@fixtures` at the top of this scenario outline:

[[[ code('830c0b7498') ]]]

That's called a tag, and you can put many as you want, separating each by a space.
At first, adding a tag does nothing except for the magic `@javascript` that changes
to use a JavaScript driver.

## Running things Before a Tagged Scenario

But in `FeatureContext` you can add an `@BeforeScenario` method that's *only* executed
when a scenario has a certain tag. Make a new `public function loadFixtures()`. Inside,
just to see if it's working, put `var_dump('GO!');`. Above, put the normal `@BeforeScenario`:

[[[ code('f1c37ba761') ]]]

Here's the trick: after this, add `@fixtures`. Now, this will only run for scenarios
tagged with `@fixtures`. To prove that, re-run our `search.feature`:

```bash
./vendor/bin/behat features/web/search.feature
```

There's our 'GO!'. Now run the `authentication.feature`:

```bash
./vendor/bin/behat features/web/authentication.feature
```

This passes with no `var_dump()`. Perfect!

## Loading the Fixtures

One way to execute the fixture is by running the `doctrine:fixtures:load` command.
I use a different method that gives me more control. Add `$loader = new ContainerAwareLoader()`
and pass it the container:

[[[ code('f7058dbac6') ]]]

Now, point to the exact fixtures objects that you want to load. There are two methods
available: `loadFromDirectory()` or `loadFromFile()`. Move up a few directories and
load from `src/AppBundle/DataFixtures`:

[[[ code('5b5d29b362') ]]]

That should do it!

Next, create an `$executor = new ORMExecutor()` and pass it the entity manager:

[[[ code('57eb8d4ee7') ]]]

A purger is the second argument, which you only need if you want to clear out data.
We're already doing that, so I'm not going to worry about it here. Finally type
`$executor->execute($loader->getFixtures())` and pass true as the second argument:

[[[ code('9369404e2e') ]]]

This says to not delete the data, but to append it instead. 

Ok, run `search.feature`:

```bash
./vendor/bin/behat features/web/search.feature
```

It fails for a completely different reason. Things are never boring here! This is
a unique constraint violation because it's not clearing out the data before loading
the fixtures. This is a funny edge case. Because the new `@BeforeScenario` is near
the top and the other for clearing the data is lower, they're being run in that order.
Move these `@BeforeScenarios` up top and keep them in the order that you want:

[[[ code('f623dde9f7') ]]]

Back to the terminal and run this sucker again!

```bash
./vendor/bin/behat features/web/search.feature
```

Pop the champagne people, it passes! It clears the data and *then* loads the fixtures.
And life is super awesome! 

## Running Tagged Scenarios

There's another benefit to tagging scenarios. But first, if you ever need some details
about the behat executable, run it with a `--help` flag to get all the info:

```bash
./vendor/bin/behat --help
```

One of the options is tags:

> Only execute the features or scenarios with these tags

Well that's sweet. So we could say: `--tags=fixtures` and it will only execute scenarios
tagged with fixtures:

```bash
./vendor/bin/behat --tags=fixtures
```

Or, we can get real crazy and say that we want to run all scenarios *except* the ones
tagged with `@fixtures` by using the handy `~` (tilde) character:

```bash
./vendor/bin/behat --tags=~fixtures
```

## behat -vvv

One more tip! If something goes wrong, there's also a verbosity option that will show
you the full stack trace. Just add `-v`:

```bash
./vendor/bin/behat --tags=~fixtures -v
```

Hey, that's all! Hop in there, celebrate behavior driven development, create beautiful
tests and sleep better at night!

See ya next time!
