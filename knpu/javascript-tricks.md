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
a fast ajax request to load this. We'll add a sleep to fake that and rerun it. 
