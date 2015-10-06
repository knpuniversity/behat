# Suites

When Behat loads it reads our step definitions from `FeatureContext` and `MinkContext`
because of `behat.yml`. This is a really powerful idea because instead of having one
giant context class we can break it down into as many small pieces as we want. We might
have one just for dealing with adding users to the database and another one for the API
just so that we have a little bit more organization. If you look at our `FeatureContext`
we already have two very different ideas mixed inside of here. We have functions inside of
here that are used to test the ls command and some that are used to test our actual website.

This is a great opportunity for us to seperate these into two classes. I'll copy `FeatureContext`
and create a new one called `CommandLineProcessContext`, update the classname and now we can just
get rid of the stuff in here that doesn't have anything to do with the command line process, which
are these last few functions right here.

In `FeatureContext` we can get rid of all the things that have nothing to do with testing our website
which includes all these functions, our before and after scenario hooks. We've really trimmed this down.
Of course to keep our tests passing we'll need to tell Behat about our new contex class. 

In theory we should be able to run `./vendor/bin/behat` and it should run all of our Behat features, the
ls and web and things should still work. Yep and it does work! It is printing out undefined definitions,
but I'll show you why in a second. 
