# Scenarios

We've created our features, which is really nice because we can see the
business value of each and decide which one to start on first. I want to
start with our product admin feature, so our admin users can start loading
in data.

Now that we've selected that, we'll start adding scenarios to the feature which
are essentially user stories. My first scenario is going to say, "when I go to
the admin area and click on products, I see the products listed." 

So let's start with:

[[[ code('725c8fa9a6') ]]]

Do keep in mind that this line doesn't matter too much, no need to be Shakespeare.

## Given, When and Then

For these scenarios I need you to think as if you *are* the admin user. We'll talk in
the first person point of view at the intended user's technical level. Meaning, clicking
on buttons an admin user can actually recognize and viewing things that they will
see. Put yourself in their shoes.

## Given: Setup

Scenarios always have three parts, the first is `Given` where you can create any type of
setup before the user story starts. So in our case, if we want to list products, we need
to make there are some in the database to see:

[[[ code('5ea4ea3ae0') ]]]

Why am I saying exactly "there are 5 products"? Great question, the exact phrasing here 
doesn't matter at all. I'm just using whatever language sounds most clear to me. 
Move on down to the next line:

[[[ code('b8454dd380') ]]]

Using the word `And` here extends the `Given` line to include more setup details. With these
two lines, we'll have 5 products in the database and start the test on the `/admin` page.

## When: User Action

The second part of every scenario is `When` which is the user action. Here,
"I" - the admin user - actually take action. In our case, the only action is clicking products:

[[[ code('af0e00b21f') ]]]

This is good because that's the name of the link and it reads very clearly. 

## User Expectations

Finally, the last part of every scenario is `Then`, this is where we witness things
as the user. In our scenario, we should see the 5 products that we set up in our `Given`:

[[[ code('5c8e68652a') ]]]

Always use language that is clear to you after your `Given`, `When`, `Then` or `And` in
your scenarios. 

## Only play God in Given

In `Given` you can add things to the database before hand, but once you are in `When` and
`Then` you should only be taking actions that the admin user could take and viewing things
they can view. This means we won't be using CSS selectors in our scenarios or phrasing such
as "Then a product should be entered into the database" because a user can't see that happen.
However, the user could see a helpful message, like "Celebrate, your product was saved!"

## BDD with a New Scenario

Let's create a new scenario for functionality that doesn't actually exist yet: adding a new product. 
Time to plan out this scenario!

[[[ code('5540e64103') ]]]

We don't need to add any products in the database before hand so we just start with:

[[[ code('79397aa8b5') ]]]

We're going to want a button on this page that says "New Product". So let's add clicking that
to the process of creating a new product: 

[[[ code('81308ed6d5') ]]]

Clicking this will take us to another page with a form containing fields for name, price
and description. To keep our `When` going to include the action of filling out this form
we'll use `And`:

[[[ code('075f0e0451') ]]]

There *is* a secret reason of why I'm using language like "I fill in" after the `And`,
which we'll cover soon. Lastly, in our process I'll expect to hit a save button to
finish creating my new product. So let's add another line to our scenario:

[[[ code('fd94e773ab') ]]]

At this point my product should be saved and I will be redirected to another page with a
success message to prove that this worked. This is where we'll say:

[[[ code('ee1609baed') ]]]

Isn't that a nice way to plan out your user flow? When we *do* finally
code this, we'll know exactly how everything should work.
