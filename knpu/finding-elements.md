# Finding Elements by CSS and Name

Back on the weird Wiki page, run an inspect element on the navigation. There's a hidden
`h1`  tag inside of the `WikiHeader` and `WikiNav` elements. Let's try to find
this and print out its text.

## Finding Elements by CSS

To do that use the `find()` function: pass it `css` as the first argument and then
use your css selector: `.WikiHeader .WikiNav h1`. 

## Important Object 4: An Element (NodeElement)

Surprise! This is actually the fourth - and final - important object in Mink. You
start with the page, but as soon as you find a single element, you now have a
`NodeElement`. What's cool is that this new object has *all* the same methods
as the page, plus a bunch of extras that apply to individual elements.

Let's dump the `$header->getText();` and re-run the mink file. Now it prints
"Jurassic Park Wiki Navigation" - so finding by CSS is working.

## Finding an Element, then Finding Deeper

Let's do something harder and see if we can find the "wiki activity" link
in the header by drilling down into the DOM twice. First, find the parent
element by using its `subnav-2` class. So I'll say
`$page->find('css', '.subnav-2');`. Oh and don't  forget your dot!

Now, `var_dump` this element's HTML to make sure we've got the right one. Run `mink.php`.
Great - it prints out all the stuff inside of that element, including the `WikiActivity`
link that we're after.

To find *that*, we need to find the `li` and `a` tags that are inside of the `.subnav-2`.
We could do that by just modifying the original selector. But instead, once you have an
individual element you can use `find` again to look inside of *it*. So we can say
`$nav->find()` and use css to go further inside of it with `li a`. The `find()` method
returns the *first* matching element.

Dump this element's text and check things. Yes! It returns Wiki Activity!

## Find via the Amazing Named Selector

In addition to CSS, there's one more important way to find things using Mink: it's called
the `named` selector. I'm going to paste in some code here: please do not write this -- it's
ugly code -- I'll show you a better way.

Instead of passing `css` to `find()`, this passes `named` along with an array that says
we're looking for a "link" whose text is "Wiki Activity". The `named` selector is all about
finding an element by its visible text. To see if this is working let's
`var_dump($linkEl->getAttribute('href'));`. That should come back as the URL to the activity
section. Try it out.

It works! The `named` selector is *hugely* important because it lets us find elements by
their natural text, instead of technical CSS classes. In this case, we're using the text
of the anchor tag. But the named selector also looks for matches on the `title` attribute,
on the `alt` attribute of an image inside of a link and several other things. It finds
elements by using anything that a user or a screen reader thinks of as the "text" of an
element.

And instead of using this big ugly block of code, you'll use the named selector via
`$page->findlink()`. Pass it "Wiki Activity". This should work just like before.

### Named Selector: Links, Fields and Buttons

The `named` selector can find 3 different types of elements: links, fields and buttons.
To find a field, use `$page->findField()`. This works by finding a label that matches
the word "Description" and *then* finds the field associated to that label. To find a
button, use  `$page->findButton()`. Oh, and the named selector is "fuzzy" - so it'll
match just *part* of the text on a button, field or link.

## Click that Link Already!

Ok! Let's finally click this link! Once you have a NodeElement, just use the
`click()` method:

Run the script:

```bash
php mink.php
```

You can see it pause as it clicks the link and waits for the next page to load. And then
it lands on the `Special:WikiActivity` URL.

## dragTo, blur, check, setValue and Other Things you can do with a Field

When you have a single element, there are *a lot* of things you can do with it, and each
is a simple method call. We've got `focus`,  `blur`, `dragTo`, `mouseOver`, `check`, `unCheck`,
`doubleClick` and pretty much everything you can imagine doing to an element.

## GoutteDriver = Curl, Selenium2Driver = Real Browser

Head back up to the `GoutteDriver` part - that was importnt object number 1. The driver
is used to figure out *how* a request is made. The Goutte driver uses curl. If we wanted
to use Selenium instead, we only need to change the driver to `$driver = new Selenium2Driver();`.
That's it! Oh and make sure you have `$session->start()` at the top: I should have had
this before, but Goutte doesn't require it. Similarly, at the bottom, add `$session->stop();`:
that closes the browser.

In our terminal, I still have the Selenium JAR file running in the background. Run `php mink.php`.

The browser opens... but just hangs. Check out the terminal. It died!

> Status code is not available from Behat\Mink\Driver\Selenium2Driver".

The cause is the `$session->getStatusCode()` line. Different drivers have different
super powers. The Selenium2 driver can process JavaScript: a pretty sweet super power.
But it also has its own weakness, like kryptonite and the inability to get the current
status code.

The driver you'll use depends on what functionality you need, which is why Mink made it
so easy to switch from one driver to another. Remove the `getStatusCode()` line and
re-run the script. Other than this annoying FireFox error I started getting today,
it works fine. The browser closes, and we're now dangerous with Mink.

Let's put this all together!
