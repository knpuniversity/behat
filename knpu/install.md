# From Install to JS Testing

Welcome to the magical world of Behat, my favorite library. No joke 
this thing is the best. Behat is about two things:

## BDD, Functional Testing and... Planning a Feature???

First, functionally testing your application. Which means writing code that will
open a browser, fill out a form, hit submit and verify text on the other side.

Why test your application? Well, imagine you are in charge of safety at Jurassic
Park, your job is to make sure guests aren't eaten by dinosaurs. You need to be 
certain that the new pterodactyl exhibit that's being put in won't turn off the
electric fence around the velociraptor pen. No tests? Good luck, they know how to
open doors. 

And second, designing your application. As developers we'll often just start coding
without thinking about what we're building or how the feature will behave.

Using behavior driven development, which Behat helps you do, you'll actually plan things
out beforehand. 

Imagine a world where communication on your team is perfect, you always deliver exactly
what your client wants, electricity on the raptor fence never goes down and chocolate
ice cream is always free. Yep, that's where we're going. 

## Behat Docs

Over in the browser, let's surf to the Behat documentation. In this tutorial we're covering
version 3, and for whatever reason when I recorded this the website still defaults to version 
2.5. So double check that you are actually looking at version 3's documentation. 

I've got our project, the raptor store, loaded here. This is where dinosaurs go for the
newest iphone and other cool electronics. There's a homepage, an admin section and that's
basically it. This store is built using a very small Symfony2 application -- but hey,
don't panic if you're not using Symfony: everything here will translate to whatever
you're using.

Cool, we've got a search box, let's look up some sweet Samsung products. And here
are the two results we have. I want to start this whole Behat thing by testing this. 
Hold onto your butts, let's going to get this thing running! 

## Install and Configuration

Over in the terminal run `composer require` and instead of using `behat/behat` we'll grab:
`behat/mink-extension` and `behat/mink-goutte-driver`:

```bash
composer require behat/mink-extension behat/mink-goutte-driver
```

These are plugins for Behat and another library called Mink and they require Behat and
Mink. We see the Mink library downloaded here,  and the Behat library downloaded
down there. So life is good! 

Once you've downloaded Behat you'll have access to an executable called `./vendor/bin/behat`
or just `bin/behat` for Symfony2 users. Running it now gives us a nice strong error:

```bash
vendor/bin/behat
```

That's ok because we need to run it with `--init` at the end just one time in
our application:

```bash
vendor/bin/behat --init
```

This did an underwhelming amount of things for us. It created two directories and one file. 

In PhpStorm we see a `features` directory, a `bootstrap` directory and a little `FeatureContext.php`
file and that's all of it:

[[[ code('3bda2edfd5') ]]]

While we're here, I'll add a use statement for `MinkContext` and make it extend that.
I'll explain that in a minute:

[[[ code('6eb54475e1') ]]]

One last bit of setup: at the root of your project create a `behat.yml` file. I'll paste in some
content to get us started:

[[[ code('2d211c6e36') ]]]

When we run Behat it will looks for a `behat.yml` file and this tells it:

> Yo! Our application lives at localhost:8000, so look for it there.

## Your First Feature and Scenario

Behat is installed, let's get to writing features! In the `features` directory create a new file
called `search.feature` and we'll just start describing the search feature on the raptor store
using a language called Gherkin which you're about to see here. 

[[[ code('c1178e9e4e') ]]]

Here I'm just using human readable language to describe the search feature in general. Within
each feature we'll have many different scenarios or user flows. So let's start with 
`Scenario: Searching for a product that exists`. Now using very natural language I'll describe
the flow. 

[[[ code('918291181c') ]]]

Don't stress out about the formatting of this, we'll cover that in detail. 

The only two things that should look weird to you are `searchTerm` and `search_submit` because
they are weird. `searchTerm` is the `name` attribute of this box here, and `search_submit` is the
`id` of this button. We'll talk more about this later: I'm actually breaking some rules. But
I want to get this working as quickly as possible. 

## Running Behat

Ready for me to blow your mind? Just by writing this one scenario we now have a test for our app.
In the terminal run `./vendor/bin/behat` and boom! It just read that scenario and actually went
to our homepage, filled in the search box, pressed the button and verified that "Samsung Galaxy"
was rendering on the next page. Why don't we see this happen? By default, it runs things using
invisible curl request instead of opening up a real browser. 

## Testing JavaScript

The downside to this is that if you have JavaScript on your page that this scenario depends on, it
isn't going to work since this isn't actually opening up a real browser. So, how can we run
this in a real browser? There are actually a bunch of different ways. The easiest is by
using Selenium. 

Grab another library with `composer require behat/mink-selenium2-driver`. You'll also need to download
the selenium server which is really easy, it's just a jar file. Click this link here under downloads 
to get the [Selenium Standalone Server](http://www.seleniumhq.org/download/). I already have
this, so I'm not actually going to download it.

To run things in Selenium, open a new tab in your terminal, and run the jar file that you just downloaded.
For me that's

```bash
java -jar ~/Downloads/selenium-server-standalone-2.45.0.jar
```

This will load and run as a daemon, so it should just hang there. 

Our library is done downloading and we just need to activate it in our `behat.yml` with the line:

[[[ code('98773813b9') ]]]

This gives me the option to use goutte to run the test using curl requests or Selenium to have things
run in a browser. By default, this will just select goutte. So how do we make it use Selenium? I'm so
glad you asked! 

Above the scenario that you want to run in Selenium add `@javascript`:

[[[ code('c3d8cb8532') ]]]

And that's it. Go back to the terminal and let's rerun this test. It actually opens the browser,
it's quick but you can see it clicking around to complete the scenario. Cool!

We write human readable instructions and they turn into functional tests, and this just barely
scratches the surface of how this will change your development. Let's keep going and figure out
what's really going on here.
