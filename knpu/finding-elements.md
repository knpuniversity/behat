# Finding Elements by CSS and Name

Our true goal is to drill down on the page and find sub elements. I'll inspect element on
the navigation and we see that there is a hidden h1 tag inside of the WikiHeader and WikiNav. 
Let's see if we can find that element and print out its text. To do that use the `find()` function. 
Pass it css as the first argument and then use your css selector which would be .WikiHeader
.WikiNav h1. 

Surprise! This is actually the fourth important object in Mink. You start with the
page, but as soon as you drill down to find a single element that's called a node element.
What's really cool is that NodeElement and DocElement extend the same base class so they mostly
have the same methods. NodeElement does have a few extras that apply to individual elements. 
So here, let's dump the `$header->getText();` and rerun mink in the terminal. And it returns 
"Jurassic Park Wiki Navigation". Beautiful!

No slowing us down now, the next challenge is to see if we can find and click this wiki activity link.
I'll do this in two steps to show off something else. First, there's a class called `subnav-2` so let's
just find that element and print out its text. We'll use `$page->find('css', '.subnav-2');` Oh and don't 
forget your dot! We'll `var_dump` this to print out its HTML to confirm that we are selecting the right 
element. Rerun Mink! There we go, it prints out all the stuff inside of there including our WikiActivity
link which is the one that we are after. 

To find this specific link we need to find the li and a that are inside of the `.subnav-2`. We could do
that by just modifying the selector here. But instead, once you have an individual element you can use
`find` again to look inside of it. So we can say `$nav->find()` and use css to go further inside of it
with li a. We're just drilling down to smaller and smaller parts of the page. Let's use our handy dump 
to get the text and run it in the terminal. Woo it returns Wiki Activity - we've done it!

Beyond CSS there is one more important way to find things using Mink. It's called the `named` selector.
I'm pasting in some code here, please do not write this -- it is ugly code -- I'll show you an easier way.
Instead of CSS this says `named` and then you pass in an array that says we're looking for a link whose text
is "Wiki Activity". The `named` selector is all about finding what is visible on the rendered page. To see
if this is working let's `var_dump($linkEl->getAttribute('href'));` and that href should come back as this
right here. Let's give that a try. 

Hey look at that, it works! The `named` selector is awesome because it finds it via the text. In our example
this is actually the text of the anchor tag. But not just the text, it looks for other things that make sense
like title attributes or alt attributes on images. Anything that we as users or screen readers think of as the
text the `named` selector finds that. Just remember it can't process CSS, just text. 

Instead of using this big ugly block of code, what you're actually going to use is `$page->findlink()`. Inside
of there put in "Wiki Activity". This will work exactly the same as before. The `named` selector works with three
different types of elements, links, fields and buttons. To find a field I'll type `$page->findField()` and this works
by finding a label that matches the word "Description" and then finds the field associated with it. Then we have
`$page->findButton()` will look for a button with this text on it. 

Named Selectors become really important the farther into Behat we get so we'll come back to this again. 

The purpose of us driving down into this link element is because we want to click it! Once you've selected
an individual element there's a bunch of actions you can take on it like click. So let's click our link element
and then we can ask the session what the current URL is after the click. Let's run it!

We can see it pause there and then it goes to Special:WikiActivity. 

As I said, there are other options beside click, which I'll highlight here in the tree. We've got `focus`, 
`blur`, `dragTo`, `mouseOver`, `check`, `unCheck`, `doubleClick` pretty much anything that you can imagine
doing to an element there's a method inside of here to do it with Mink. Mink's whole purpose is to surf to
pages, drill down to elements and then take an action on them. 

Heading back up here to the goutte driver, the driver is used to say how a request is made and the goutte
one is used to say "make background curl requests". If we wanted to use Selenium instead, we don't need
to change any of our code, just the driver to `$driver = new Selenium2Driver();`. That's it! Oh and make
sure you have `$session->start();` up here, I should have had this before with the goutte driver even though
it doesn't depend on it. This opens up your browser. And once we have that at the top, at the bottom of the
file we need to add `$session->stop();` which will close the browser. Again, you should always have that in
there even if you're using goutte. 

Over in our terminal our Selenium java is still running in the background. Since we're here let's run
`php Mink.php` and it opens up our browser but it just hangs here. Let's check to see what's going on.
If you look it actually died, it says "status code is not available from Behat\Mink\Driver\Selenium2Driver".
It's dieing because of the `$session->getStatusCode()` right here. Different drivers have different super powers,
the Selenium2 driver processes javascript as its super power. But it also has it's own downsides like a weakness
for kryptonite and it's inability to get the current status code.

Depending on what functionality you need that's why you would switch from one driver to another since the goutte
driver has no issue getting the current status code. So if we remove the statusCode line and rerun Mink, other than
my silly FireFox error it will work. The browser closes, and there we go!

You now know Mink, so let's put this all together!

