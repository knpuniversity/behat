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
so if you have Jquery on your page you can use it. Any other javascript that you have on your page is available
to you right here. 

Let's run it again, and we should see it run only as slow as it needs to. This time it started filling out the
fields a lot faster. You now have one of the biggest tools in testing javascript, getting proper waits. 

What about debugging? Let's say we messed something up here in our scenario. Instead of calling this the
"Name" field we called it "Product Name", we know that this is going to fail. The issue is that it is going
to open up the browser and fail really quickly, too quickly to see exactly what went wrong. In the terminal
we get an error telling us that there isn't a field called "Product Name" but with nothing else to look at to
determine if that is because there was an error on the page, are we on the wrong page, is the field called 
something else? Why won't someone tell us what's going on!?

To clear things up for ourselves, first google for "behatch contexts". This is an opensource library that has
a bunch of useful contexts, as in `FeatureContext` and `MinkContext`, we've got something here called
`BrowserContext` that you could bring into your project that will add a lot of useful definitions to your project.

I don't typically use this library but I do steal from it. Inside of `DebugContext` lives one of my absolute
favorite step definitions, `iPutABreakPoint`, copy that and drop it into our `FeatureContext` file, or you 
could even create your own `DebugContext` if you wanted to. I like to shorten this to "I break". The way to
start using this is just like the `print last response` step. Our error is on "Product Name" so I'll drop it
in just above that step. 

     And break

The "I" part of this step is optional. Ok, back to the terminal to rerun our test so we can start to debug
what is wrong with our "Product Name" field. The modal pops open, and the browser freezes there. Over in
the terminal there's a message "Press [RETURN] to continue..." It's just sitting there waiting for us to
look at this page and debug what the issue is. Once we've checked out everything we wanted to in the browser
we'll go and hit enter on our test to keep it moving along.

That is the number one debugging tool for javascript. 

But there are more cool things, like `iSaveAScreenshotIn`. Copy that definition and paste it into `FeatureContext`,
change the phrasing to "I save a screenshot to" and remove this `screenshotDir` thing since we don't have that and
replace it to save things to the root of our project with `__DIR__'/../../'`. Back over in our scenario say,

     And I save a screenshot to "shot.png"

Run it! Our modal pops open and it should still be failing at our "New Product" step, the browser closes, we
check our terminal, which confirms for us that this test is still failing. But over in our IDE we can see
at the bottom of our tree a `shot.png` file which shows exactly what things looked like when the test failed.
Woah.

When you have continuous integration setup on your project you can use this to help you figure out what
causes builds to fail, which normally is really difficult to debug. By using this hook system, `BeforeScenario` 
and `AfterScenario` we can actually have a function that is called everytime after one of your scenarios
fail. In that you can automatically save a screenshot on every failure. In a project I worked on we had
a setup where a screenshot was taken on every scenario failure and uploaded to an Amazon S3 instance, so
later when we were looking at Travis wondering why a scenario failed we could just head over to the S3 bucket
for that and download the screenshot. So helpful.

Anyways, let's remove this and change "Product Name" back to "Name" and we again have a functional test for
our javascript modal. 
