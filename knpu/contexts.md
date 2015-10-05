# Contexts

Behat allows us to write human readable scenarios and Mink is really good at
browsing the web you can see that if you put them together you could start having
steps that look a lot like what we have in the search feature here. 

    Given I am on "/"

We know in the background that we can write a step definition that would use Mink's 
session object to actually go to that URL. Back when we ran this scenario it was
already working so there's already some integration going on behind the scenes
between Behat and Mink. I want to show you how's that's happening.

First, in `FeatureContext` I had you extend this `MinkContext` class, go ahead and
remove that now. 

When Behat runs it needs to know all of the step definitions that it has available.
You can see that list by passing a `-dl` to the Behat command. Here we can see
our four built in step definitions. This is going into our `FeatureContext` class,
parsing out all of these `@Given`, `@When` and `@Then` code blocks and printing 
them out here as a list of all the things we can use inside of our scenarios right now.
If we add more step definitions then this list will grow. 

If we use something that isn't in this list Behat very politely prints out the function
for us in the terminal. 

In `behat.yml` we initially need this MinkExtension, that's what ties Behat and Mink together.
That's going to give us two things, access to the Mink session object inside of our `FeatureContext`
class and one other thing. 

Below, add a new config called `suites:` and a config under that called `default:` and a `contexts` key.
We'll go more into the details about `suites` in a bit. Under the `contexts` key I'll add `FeatureContext`
and the one we just deleted `Behat\MinkExtension\Context\MinkContext`. When Behat loads it wants to know
all the context classes it should look inside of to parse out these commands here. By default it looks for
a `FeatureContext` class but by adding this config here we say look inside there and inside this `MinkContext`
class. 

Back over in the terminal let's run the bin behat -dl command again. And boom, we have a huge list of items 
in there! These are all kinds of things that we do typically with the web like `When I go to` or
`When I fill in field with value`. It's all the things you should recognize that we were already using 
inside of our `search.feature`. Where did this come from? `MinkContext` of course! If you're using PHPStorm
use the shift shift shortcut and search for `MinkContext` and you can see that it looks just like our context
file but it has a whole bunch of these things already filled in, tons of free functionality! So, why did I use
this exact language inside of my scenario originally? Because, I wanted to be lazy and reuse the step definitions
that I knew we were going to get for free. 

I'll take off the `@javascript` since we don't need that and we should be able to run our search feature. Perfect!
