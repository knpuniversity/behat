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
but I'll show you why in a second. Here's the search feature working correctly and here's the ls feature
working correctly. 

Remember, we also have a Product Admin Area feature but we haven't filled it out yet, but we'll fill this
all in shortly. 

Seperate things into multiple contexts to keep yourself sane. 

We can go further, back in `behat.yml`, you see we have this `suites` key here and we have one suite
called `default`, but you could have many. What the heck is a suite? It's a combination of a set of
feature files and the contexts that should be used for that. In our case, the `ls.feature` is the only
feature that needs the `CommandLineProcessContext`. All the other features which are for our website,
need the `FeatureContext` and the `MinkContext`. This is the perfect opportunity to create a second suite
which I am going to call `commands`. To this I'm only adding the context `CommandLineProcessContext`. 
Up here we can remove that from the default suite. 

When you execute Behat it uses the default suite unless you tell it which one to use with the --suite option. 
We can access our commands suite with `--suite=commands` and then we can run our feauture. Or we can do the
-dl option to see the only the definition lists associated with that context file or if we run `./vender/bin/behat -dl`
we'll see all the other ones. 

We can take this a step further by telling our suites which features belong to them. Under the features directory
create two new directories, one called `commands` and another called `web` and we'll organize things a little
bit here. We'll put `ls.feature` inside of `commands` and the other four inside of `web`. This doesn't change anything
but now inside of our `default` suite we can add a `paths` key where we can use a nice little trick called
`[%paths.base%]` which defaults to the root of the project, `/features/web`. Then in the commands suite do the same
thing but change the paths key to look into the `commands` folder.

The purpose of doing this is so we can just run behat which uses the default suite it knows to find the features
inside of the web directory only. We don't see the `ls.feature` stuff in here at all. Then if we run it with 
`--suite=commands` it's only going to run the stuff inside its directory.

So if you have two very different things that are being tested then it's a good idea to seperate them into
different suites entirely.

