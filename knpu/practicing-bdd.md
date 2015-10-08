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

