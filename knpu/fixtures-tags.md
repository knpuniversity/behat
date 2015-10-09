# Tagging Scenarios in order to Load Fixtures

Remember that search feature that we started with? Let's try running that one again.
`./vendor/bin/behat features/web/search.feature`. Huh, this one is failing now.
It says that the text "Samsung Galaxy" was not found anywhere on the page. At this point,
you are an expert so it should be obvious that we are not setting up the data in this case.

This worked originally because the fixtures that come with our project have "Samsung Galaxy"
but now that other tests have cleared that out it's not there. To be good developers we need
to put the data in before hand.

We know that we could put some `Given` statments here to put those products in. But sometimes
when we run scenarios we just want to run the fixtures that we created for the project. I've
got this `LoadFixtures` class that's responsible for putting in the Kindle and Samsung products.

When I click "Reset DB" it's actually loading the fixtures. So, if you want to do this, and I'm not
recommending that you do because entering in the data manually with the `Given` is the most readable
way to do things. But, if you do want to do this here's how. First of all I don't want to load
fixtures before every single scenario, that's going to cause extra overhead of stuff in the database
that we don't need. 

What I need is a way to tag scenarios to say "this one needs fixtures" . So I'll try this by putting
`@fixtures` at the top of this scenario outline. That's called a tag and you can put many of them
here just by doing this. Most tags at first don't have any functionality, `@javascript` does but
`@foo` and `@fixtures` don't do anything to our project.

In `FeatureContext` we can have `BeforeScenarios` that are only run for certain tags. Let's make a new
`public function loadFixtures` and inside of here just to see if it's working I'll `var_dump('GO!');`
And above our function we'll put the normal `@BeforeScenario`. The trick is that after this we add
`@fixtures`, this will only allow this to run if the scenario being executed has this tag. To prove that
this is working let's rerun our search.feature test. Hey there's our 'GO!'. But, if we run the authentication.feature
the test passes and we don't see the var dump. Perfect!

There are a number of different ways to load our fixtures. One way is to execute Symfony's doctrine:fixtures:load command.
I'm going to go a bit lower and do it in a more manual way. `$loader = new ContainerAwareLoader` and passing it the
container and then pointing to the exact fixtures that I want loaded. We have two methods available, load from directory
or load from file. This is doctrine stuff, and I'll go up a couple of directories and load it from `/src/AppBundle/DataFixtures`. I think that should do it!

Next, create an `$executor new ORMExecutor()` pass it our entity manager, a purger is the second argument
which you only need if you want to clear out data. We're already doing that before our scenario so I'm not
going to worry about it. Finally type `$executor->execute($loader->getFixtures())` and pass true as the
second argument. This says to not delete the data but to append it instead. 

Let's run the search.feature test again, and it actually fails for a completely different reason. Things
are never boring here! We're having a unique constraint violation because it's not actually clearing out the fixtures.
This is a funny thing that doesn't happen very often but because this `@BeforeScenario` is up here and the other
is further below in the file it's actually running in that order. This might seem a little weird, but we'll just
move these `@BeforeScenarios` up top and keep them in the order that I want them. 

Back to the terminal and run this sucker again! And pop the champagne people, it passes! It's clearing our data
then loading our fixtures and life is super awesome! 

One more cool thing with tags that has to do with Behat itself. But first, if you ever want to know about Behat
just run it with `--help` and it'll print out all the options that you can run when you execute it.

One of these options is tags, "Only execute the features or scenarios with these tags". For example we can say
`--tags=fixtures` and it will only execute tests with fixtures. Or, we can get real crazy and say everything
except ones with fixtures with this handy little tilde character.

It'll start to execute you can see it's going through the authentication test. I'll stop it before it runs through
all of that. 

The last little bit of information I want to give you is that if something goes wrong there's also a verbosity option
here, which will let you see the full stack trace if something is going wrong in your test. Just pass the `-v` 

Hey, that's y'all! Hop in there, celebrate behavior driven development, create beautiful tests and sleep better at night!

See ya next time!
