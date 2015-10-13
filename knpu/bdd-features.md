# BDD Features

I'm sure you've heard of "Test Driven Development" where you write
the tests first, and you code until those tests pass. That process is
really cool! But we're talking about "Behavior Driven Development" or
BDD. This is the idea that you are going to write down the behavior of your
application first and then develop it. 

## Behavior-Driven Development

This might sound pretty obvious to you: why would I code without thinking about
what I'm going to code? But that actually happens pretty often. 

Behat and BDD are going to give us a very specific path to follow so that we think
first, and then we code. We do this to maximize business value. There are two types
of BDD, story and spec. Without getting too far into the details of each, story BDD is
done with Behat and usually ends up with functional tests. Spec BDD is handled by another
wonderful tool called [PHPSpec](http://www.phpspec.net/) and this ends up focusing
on unit testing, how you actually design your classes and functions. They're both
cool and in a perfect world you'll use both.

One minute of theory:

With BDD we break down our development process into four steps:

1. Define the business value of all of the big feature.
2. Prioritize those so you work on the ones with the highest business value first.
3. Take one feature and break it down into all of the different user stories or scenarios.
4. Write the code for the feature, since you now know how you want it to behave.

To do all this planning, Behat uses a language called **Gherkin**. This isn't special to Behat or PHP,
it also exists in Ruby, Java and every other programming world out there which is a good thing. 

## Writing Features

Our application is already partially built, but let's pretend that it isn't and we're just in the
planning stages. First we know we'll need an authentication feature. So let's go into the `features`
directory and create a new `authentication.feature` file. Each feature will have it's own file in here:

[[[ code('f21554628c') ]]]

We always start with the same header `Feature` and a short description of it. Here we'll just say
"Authentication". The next three lines are very important. Our next line is always "In order to"
followed by the business value. In the case of the Raptor Store, since you need to login to see 
the product area, I would say that the business value is "to gain access to the management area".

Next is "As a" and you say who is going to benefit from this feature, in our case it would be an
admin user. And the third line is, "I need to be able to" followed by a short description of
what the user would actually be able to do with this feature. In our store that is "login and logout".

The most important parts are the first two lines, the business value and the user - or role - that's
going to benefit from this value. If either of these are difficult to define then maybe your feature
isn't actually valuable. Or maybe none is benefiting from it. Perhaps move onto a different task.

## Writing Good Business Values

Back in the Raptor store, login with admin/admin and check out the product admin area. Let's describe
that! Back into the `features` directory and create a new `product_admin.feature` file.  And we'll
start the same as we always do:

[[[ code('d777b47ac7') ]]]

So why do we care about having a product admin area? It's not just so we can click links and fill out
boxes. Nobody cares about that. The true reason to go in there is to control the products on the
frontend. So let's say just that:

[[[ code('b35e47ba49') ]]]

## Writing at the Tech Level of your User

That looks good. What else do we have? Check out the "Fence Security Activated" message on the site.
Let's imagine we need to create an API where someone can make an API request to turn the fence
security on or off from anywhere. For example, if you're running from dinosaurs somewhere, you
might want to pull out your iphone and turn the fences back on. 

We'll need another feature file called `fence_api.feature`. Start with:

[[[ code('26de64fdbc') ]]]

The business value is pretty clear:

[[[ code('acb710a390') ]]]

The user that benefits from this isn't some browser-using admin user: it's a
more-advanced API user:

[[[ code('b642a1bae2') ]]]

I feel safer already.

## Bad Business Value

There's a common pitfall, and it looks like this:

[[[ code('e68cbc8e28') ]]]

Notice the `In order to` and the `I need to be able to` lines are basically the same. This
is a sign of a bad business value. Being able to add/edit/delete products is not a business
value. People don't go into the product admin area just for the delight of adding, editing
and deleting products. They go into the admin area because that allows them to control
the products that are rendered on the front end. 

This is subtle, but really important because when we build the admin area, it will focus our
efforts: we know that we're building this just as a tool so that you can control things on the
frontend. Which sadly for the developer means we maybe don't need that crazy drag-and-drop interface
for adding videos from YouTube. Our admin user - who we're building this feature for - doesn't
care about using it, and it might be too technical for them anyways.

Focus on your business value, and keep the feature at the technical level of who you're
building it for.
