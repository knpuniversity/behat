# Practing BDD: Plan, then Build

One of the most beautiful things about Behat is having the opportunity to do
behavior driven development. The process where you write the feature first,
the scenario second and then you code it. 

So far we haven't really been doing that. We've been using our existing site features
to write our scenarios. That's fine and sometimes you'll do that in your real 
development life, but now it's time to walk through the process of actually using
behavioral driven development. 

In the "Add a new product" scenario we're outlining a feature that does not yet
have any functionality on our site. This is a feature that we thought through 
earlier to outline what the behavior should be, and I came up with this. Oops,
I just noticed a typo here, this should be,

    When I click "New Product"

Now that that is fixed, let's make this come alive by running line 19 in our terminal.
This is the exciting part! The first thing that fails is the line "When I click 'New Product'",
that makes sense since I don't have that button here yet. But there is something else
going on as well, let's add an "And print last response" line to see it. 

Ah, of course we completely forgot to login. Let's start cleaning this up with that piece. 
Copy and paste it from the scenario above. We didn't think of this during the design phase
but clearly we need to be logged in. When we rerun this it's going to fail again on the
new product link because that's still missing. Let's add one!

Open up the template for this page which is `list.html.twig` and a link would look real nice
right here. I won't get it an `href` yet, I'll just put in the text "New Product" and make it
look nice with some CSS classes and a little icon. Perfect! Let's refresh, and there's our new
button. Even though it doesn't go anywhere yet let's give it a try. 

Run the test again. I want to do as little work as possible to get each step in my scenario to pass.
This time it was able to click "New Product" but of course it doesn't see any fields called "name" 
which doesn't surprise us because this link doesn't have an `href` to send you anywhere. 

The next step in getting this test to pass is to create a page to add new products. In `ProductAdminController`
make a new `public function newAction` make it's URL `/admin/products/new` and name the route
`product_new`. Inside of here render a template called `product/new.html.twig` Easy enough!

In the `products` directory create a new file to go with this called `new.html.twig`. Look at that
nice fresh file, get it started by extending `layout.html.twig` and adding twig blocks. Now for the 
real work. In our scenario we're failing because we're missing a form to submit. So it's safe to say
that our file here needs a form tag. Have this submit right back to this same URL and add `method="POST"`

I am not going to be using Symfony's form system inside of here because (a) we don't have to and (b) my
only goal is to get these tests passing. If you want to use Symfony's form system in your project here's 
where you would start doing it!

To keep this moving I'll paste in a bunch of code that creates the three fields we're looking for, `name`,
`price` and `description`. The important thing here is that those names match up with the labels. 
We even have proper for tags setup that match the id's which is how mink will know that the label goes
with it's form field. 

And down here at the bottom we have our save button which takes care of our second to last scenario step here.

Cool! We did as little work as possible, so let's check to see if our test is passing yet. It's still failing
on one of the form field steps. This should seem weird but if you debug this you would remember that we
never filled in the `href` to go to our `new.html.twig` page. My bad.

Let's try this again. Awesome, we got further this time! We got all the way through pressing save but we aren't
seeing the flash message "Product Created FTW!" because our form isn't processing yet. No problem, let's add that!

To do that, add Symfony's request object, type hint that here. Inside of our `newAction` function add a simple 
`if ($request->isMethod('POST'))` then we'll do our form processing. To be as lazy as possible what if we only
show that flash message? My site already has some flash messaging functionality, so I'll drop in the message
that our test is looking for, `$this->addFlash('Success', 'Product created FTW!')`. Wrap this up by redirecting
the user to the product page. So, I'm not actually saving, but this will actually make the tests pass and our
admin user really frustrated when their new products don't appear in the list. 

Now, don't be a big jerk and make your tests pass without the actual feature functionality. In this case I
would improve my scenario with,

     And I should see "Veloci-chew toy"

Once we have added that product this feature isn't working until it shows up in the product list. 

Behavior Driven Development says, add a step to your scenario, watch it fail then code until that step passes.
So let's code to fix our new failing step by adding `$product = new Product();` and some code to set
the name of the product that I can copy and paste for Price and Description. This doesn't have form validation,
so you would do more work than this in real life but it gets the job done. 

To wrap this up we'll type `$em = $this->getDoctrine()->getManager();`, persist and flush. 

If we did all that correctly, our tests should pass. Excellent, look at all that green! In fact, we should
be able to go over to our site and see the Veloci-chew toy for $20. But we have a problem, it says that
the author is 'anonymous', this should be 'admin' since I created it under that user. That's definitely 
a bug we didn't think about. We didn't set the author in our `ProductAdminController`, but before we go
and code this instead we should first add a step to our test to check for this. 

     And I should not see "anonymous"

This is safe to say since there should only ever be one product created by the admin when we run this scenario. 
Back over to the terminal and run the test. Our new step fails, phew! I would have been confused otherwise. 

In `ProductAdminController` let's now set the author when a product is created, `$product->setAuthor($this->getuser());`
which looks for the current logged in user. When we run our test again it passes. 

And that folks is behavior driven development, and how I would love to see you plan out and create your projects.
It forces you to design the behavior of your code first and second, it helps you know when you're finished. Once
the tests are green you don't need to keep coding and perfecting a feature because it passes the test. Now, you 
might have another person who will handle design or maybe you will do that yourself. But from a behavioral 
perspective, this feature does what it needs to do so you can move onto what's next. 
