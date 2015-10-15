# Mink

Forget about Behat for a few minutes - I want to talk about a totally different
library. Imagine if there were a simple PHP library that let you take command
of a browser: surf to pages, find links with CSS, click those links, fill out
forms and *anything* else you can dream up. It exists! And it's called Mink.

This is such a cool library that it deserves some direct attention. Start
by creating a `mink.php` file right at the root of your project. And require
composer's autoload file:

[[[ code('82dab02f19') ]]]

We'll use Mink all by itself, outside of Symfony, Behat and everything else
so we can focus on just how *it* works.

## Important Object 1: The Driver

As awesome as Mink is, it only has four important objects. The first is the
driver. Create a new `$driver` variable and set it to `new GoutteDriver()`:

[[[ code('b98a0380b1') ]]]

Now, ignore it: I'll come back and explain important object number 1 in a
little while.

## Important Object 2: The Session

Let's move on to important object #2, and the first that we really care about:
the session. Add `$session = new Session()` and pass it the `$driver` as
an argument:

[[[ code('f4233a4ff8') ]]]

Think of the session like a browser tab: anything you can do in a tab, you can do
in a session. And actually, that isn't very much. You can visit URLs,
refresh, go backwards, go forwards and that's about it. Let's use it to visit
a very awesome and absurdly-designed site "jurassicpark.wikia.com". After that we'll
just print out a few things about the page like the status code, and the current URL:

[[[ code('e558213f96') ]]]

To execute this, head over to the terminal and run:

```bash
php mink.php
```

Look at that: it's printing out 200 and a slightly different URL than we put in our code.
That makes sense: when you go to the site in a browser, it redirects to the URL
we see in our terminal. Mink is emulating a real browser by following redirects.
But in reality, so far, it's making invisible cURL requests: it's not using a
real browser.

## Important Object 3: The Page (DocumentElement)

The third important object is called the page. Grab it by saying `$page = $session->getPage()`:

[[[ code('24eebdad21') ]]]

I want you to think of this as the JQuery object or the DOM. Anything you can do
with JQuery - like select elements, click links and fill out fields - you can do
with the page. Less impressively, it also knows the HTML of whatever page we're currently on.

***TIP
If you like to dig into the source code, the page is an instance of `DocumentElement`.
***

Let's use it to print out this first bit of text on the page with
`var_dump(substr($page->getText()), 0, 75);`:

[[[ code('d8c63a47f9') ]]]

Run that again in the terminal.

```bash
php mink.php
```

Now we see the thrilling text of: "Park Pedia - Jurassic Park, Dinosaurs,
Stephen Spielberg...". There's some weird `a:lang` code stuff on the end.
Open up the source on the page.

The `getText()` method returns anything *other* than the HTML tags themselves. The
first part comes from the `title` tag and then it grabs some other stuff from the
`style` tag, which is technically text.

But what we *really* want to do is find individual elements so we can click links
and fill out fields. Let's talk about that next.
