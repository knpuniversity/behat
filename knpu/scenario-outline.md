# Scenario Outline

Add a second scenario to our file here, this will test searching for a product
that is not found. Let's start by copying and pasting the one we've already created.

   Scenario: Searching for a product that does not exist

We will have the same `Given` in each of these scenarios so let's move those to a
`Background`. It's ok not to have any `Given`'s in the scenario and start with
`When` instead. Change the search term in the `When` line in our new scenario to
"xbox". This is really going to bum out any dino's that are looking to play Ghost Recon.

Let's find out what we should see for our `Then` line by actually searching this on our site.
"No Products Found", cool, so let's plug that into our `Then` line. Now let's run this!

We didn't use any new language or steps so everything is able to run and it even passes!

To remove duplication in our second scenario we can take things a step further with a 
Scenario Outline, which looks like this. I'll copy one of my scenarios and change it to be
`Scenario Outline:`. And wherever we have wildcards like "samsung" or "xbox" we'll replace it
with a `<term>` placeholder that has brackets on both sides. 

Down here for the "should see" we're going to replace it with another one called `result` surrounded
by brackets. Those aren't important other than that down here we're going to add a new `examples` key
with a little table
