# Master JavaScript Waits and Debugging

Let's talk javascript and the complications it can cause. Right now these are all running
the goutte driving using background curl request. This means that if we are testing some
javascript, it won't run which could give us false test failures. So we would need to use
Selenium in those instances.

First, I want this New Product page button to not load a whole new page but instead open up
a modal. I've already done most of the work to set this up. Inside of `list.html.twig` 
add the class `js-add-new-product` to our New Product button, this triggers some javascript
that I have at the bottom of this file. The other thing we need to do is to make sure that our
`new.html.twig` template returns a partial page, by removing our extends and Twig block body 
tags, since this is going to load via ajax. 

There are several ways to get this to load in a modal via ajax using different frontend libraries,
I've just used the easiest thing for me to get this working. Clicking the New Product button opens
up my modal, and it will even save my $34 Foo product. Simple!

We just modified our code, so we should rerun our tests before we deploy it to make sure that
everything still works. Let's check line 19. When we run it, it works, no problem. But there
is a gotcha here. The modal didn't actually load in the background, this test was run without
javascript. 

What our test did was click into this URL, went to this silly page, filled out the form and
hit save. That's kind of ok, it did test the form's functionality. But for our intended end
users this button is going to open a modal so I want to test this via javascript to make
sure that's working. To do that add `@javascript` to the top of the scenario. 

That changed bumped our test down to line 20, so we'll put that into our terminal when we
run this. Just be sure to have the Selenium server running in the background, java-jar. 
Watch closely as we run this test. Well don't watch that silly Firefox error. Selenium logs
in, goes to the products page, clicks the New Product button, fills in the form fields, hits
save and it worked perfectly. 

Let's complicate things again! In our `ProductAdminController` let's say this isn't such
a fast ajax request to load this. We'll add a sleep to fake that and rerun it. Our test 
logs in, clicks the button and closes the browser. We didn't see it fill out any fields,
Up here you can see that the "And I fill in name with 'Veloci-chew toy'" step has an error
that says a name field was not found. What is this madness? If you click a link or submit
a form and it causes a full page refresh Mink and Selenium will wait for that page refresh.
But, if you do something in javascript Selenium is not going to wait. It clicks the "New Products"
button and immediately looks for the name field, if that field isn't there instantly then it doesn't
work. We have to make it wait in between clicking the button and filling out the field. 

To make this happen we'll add a new step like,

    And I wait for the modal to load

This step doesn't have a definition yet, so we'll need behat to generate that for us, run the test
to get that. Selenium pops open the browser, but this will fail just like last time. Ah and there's
our new definition, copy and paste that into `FeatureContext`. Now, how do we wait for things? There
is a wrong way and a right way. Let's try the wrong way first, which is `$this->getSession()->wait(5000);`
that's going to wait 5000 miliseconds, that seems like way too long since we know our sleep is set to
one second. But let's try this out anyways to see if it passes. 

The test logs us in, clicks the button 1...2...3...4...5...6...7...8, then it actually fills in the fields. 
Excellent, it passed, but it took too long. If you start littering your test suite with these wait statements
your tests are going to start taking a really long time to run and then guess what, you won't run them, the
fences will go down and guests will get eaten by dinosaurs in your park which defeats the whole purpose of writing
them in the first place. Do you want your guests to be eaten? No. I didn't think so. Let's look at the right way 
to do this. 

Pass a second argument to wait which is a javascript expression that will run on your page every 100 miliseconds 
and as soon as it equates to true it will stop waiting and move on to the next step. In this case, when we load
the modal what it's actually doing is making something with the class `modal` visible. In jquery we cand do something
like, `('.modal:visible').length` and of course that's one. Close the modal and rerun that, it's 0. So, this jquery
code is an expression that we can use as our second argument. Now our line here says, "wait until this javascript
expression is true or wait up to 5 seconds." Why am I allowed to use the jquery here? Becuase this is run on our page,
so if you have Jquery on your page you can use it. Any other javascript 
