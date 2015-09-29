# BDD Features

I'm sure you've heard of "Test Driven Development" where you write
the test first, and you code until those tests pass. That process is
really cool! But we're talking about "Behavior Driven Development" or
BDD. Which is the idea that you are going to write down the behavior of your
application first and then develop it. 

This might sound pretty obvious to you, why would I code without thinking about
what I'm going to code? But that actually happens pretty often. 

Behat and BDD are going to give us a very specific path to follow so that we think
first, and then we code. We do this to maximize business value. There are two types
of BDD, story and spec. Without getting too far into the details of each, story is
done by Behat and usually ends up with functional tests. Spec BDD is handled by another
wonderful tool called PHPSpec and this ends up focusing on unit testing, how you actually
design your classes and functions. They're both cool and in a perfect world you'll use both.

One minute of theory:

With BDD we break down our development process into four steps. One, define the business value 
of all of our features. Two, prioritize those so you work on the ones with the highest business
value first. Three, take one feature and break it down into all of the different user stories or
scenarios. Four, write the code for the feature since you now know how you want your application to
work. 

To do all this planning Behat uses a language called Gherkin. This isn't special to Behat or PHP,
it also exists in Ruby, java and every other programming world out there which is a good thing. 

Our application is already partially built, but let's pretend that it isn't and we're just in the
planning stages. First we know we'll need an authentication feature. So let's go into the `features`
directory and create a new `authentication_feature` file. Each feature will have it's own file in here.

We always start with the same header `Feature` and a short description of it. Here we'll just say
"Authentication". The next three lines are very important. Our next line is always "In order to"
followed by the business value. In the case of the Raptor Store, since you need to login to see 
the product area, I would say that the business value is "to gain access to the management area".
Next is "As a" and you say who is going to benefit from this feature, in our case it would be an
admin user. And the third line is, "I need to be able to" followed by a short description of
what the user would actually be able to do with this feature. In our store that is "login and logout".

The most important parts are the first two lines, the business value and the user that's going to
benefit from the business value. If either of these are difficult to define then maybe your feature
isn't actually valuable or noone is benefiting from it, perhaps move onto a different task.

Back in the Raptor store, login with admin/admin and check out the product admin area. Let's describe
that! Back into into the `features` directory and create a new `product_admin.feature` file.  And we'll
start the same as we always do:

    Feature: Product Admin Area
    In order to 

So why do we care about having a product admin area? It's not just so we can click things around in there,
the true reason to go in there is to control the products on the frontend. So let's say just that:

   In order to maintain the products shown on the site
   As an admin user
   I need to be able to add/edit/delete products

That looks good, what else do we have? This "Fence Security Activated" thing here, Let's imagine we need
to create an API where someone can make an API request to turn the fence security on or off from anywhere. 
For example, if you're running from dinosaurs somewhere, you might want to pull out your iphone and turn
the fences back on. 

We'll need another feature file called `fence_api.feature`. 

    Feature: Remote Control Fence API
    In order to

What's the business value of having this feature?

    In order to control fence security from anywhere
    As a 

Now this isn't an admin user, this is someone more advanced, some sort of an API user. 

    As an API user
    I need to be able to POST JSON instructions to turn the fence on/off

I feel safer already.

Let's get into a few examples of bad features. In product admin this would be a bad feature:

    In order to add/edit/delete products
    As an admin user
    I need to be able to add/edit/delete products

Notice the 'In order to' and the 'I need to be able to' lines are exactly the same. That is 
going to be a problem. Being able to add/edit/delete products is not a business value. People
don't go into the product admin area just for the delight of adding, editing and deleting products.
They go into the admin area because that allows them to control the products on the front end. 

This is really important because it will focus us when we build the admin area to know that we're
building this just as a tool so that you can control things on the frontend. Which sadly for the
developer means we don't need tons and tons of awesome features because our admin user who is benefiting
doesn't care at all about those.
