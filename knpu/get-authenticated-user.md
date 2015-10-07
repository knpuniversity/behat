# When *I* do Something: Handling the Current User

Login is tested, let's now head over to the Product Admin Feature. We built
this out when we were just thinking about making really nice scenarios. Now we know
that most of the language that I used inside of here was because I am familiar with
the built in definition list. 

Let's get these scenarios passing! I'll start with running just the "List available products"
scenario on line 6. To do that type `.vendor/bin/behat` point to the file and then add `:6`. 
The number, in our case 6, has to be the line that the scenario starts on, and it runs only that
scenario.

We're getting a print out of our missing step definitions, so go head and copy those and paste
them in the `FeatureContext` class. 

For the `thereAreProducts` function change the variable to `count` and create a for loop and
inside of here create some products. And set some data on here. Notice, we said there are 5
products, we didn't say what those products are called because we don't care which is fine. Only
include info in your scenario that you actually care about. Because we don't have any detailed
information here we can create some random data. Since we'll need to get the entity manager
a lot let's create a `private function getEntityManager()` `return $this->getContainer()->`
there's actually a service that represents the entity manager. Perfect! 

Now back up in our `thereAreProducts` function type `$em = $this->getEntityManager();` and
the usual `$em->persist($product);` and an `$em->flush();` at the bottom. Easy stuff now that
we've got Symfony booted. 

Next thing, `iClick`
