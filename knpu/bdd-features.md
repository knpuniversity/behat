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
what the user would actually be able to do with this feature. 
