Mink
====

Now for something totally different! You've just learned how Behat allows
you to execute your Gherkin scenarios by reading each step and executing
a function that has a regular expression matching it. There are more nice
features to learn about, but Behat is just that simple.

Mink is a totally different library that helps you command a browser
using a really nice PHP interface. It has nothing to do with Behat, but will
integrate with it nicely later. Mink by itself is one of my *favorite* PHP
libraries, so it deserves to get some of its own time.

To keep things simple, let's keep building on the same project we've started.
To install Mink, just add it to your ``composer.json`` file. You should also
include two more entries for the `Goutte`_ and Selenium2 drivers. You'll
see why these are important in a second:

.. code-block:: json

    {
        "require": {
            "behat/behat": "2.4.*@stable",
            "behat/mink": "1.4@stable",
            "behat/mink-goutte-driver": "*",
            "behat/mink-selenium2-driver": "*",
        },
        "minimum-stability": "dev",
        "config": {
            "bin-dir": "bin/"
        }
    }

To actually download the libraries, run ``php composer.phar update behat``.

Imagine if you could control a browser using PHP code - telling the browser
to go to pages, click on links, fill out form fields, and grab text or other
details from each page. That's Mink. And since we've installed it via Composer,
we can just create a new file, require ``vendor/autoload.php``, and start
playing with it::

    <?php

    require __DIR__.'/vendor/autoload.php';

The Driver
----------

Mink is a small, but potent library that has exactly 4 important objects.
The first is the driver, which will make more sense later. For now, instantiate
a new ``GoutteDriver``::

    use Behat\Mink\Driver\GoutteDriver;

    $driver = new GoutteDriver();

This says that we want our "browser" to actually use Goutte, a "headless"
browser, which is a fancy word for a browser that just makes background CURL
requests. This is important object #1, and it's actually not very important.
So... forget I even mentioned it at all!

The Session
-----------

Important object #2 - and the first we care about - is the Session! You can
think of the Session as your browser and use it the same way::

    $session = new \Behat\Mink\Session($driver);

It has methods like ``visit``, ``getCurrentUrl``, ``back``, ``setCookie``
and a few more. In fact, let's google for the `Mink API docs`_. I'm not always
a huge fan of API docs, but Mink is a small library, and usually all you
really need to know is what methods you can call on our 4 important
objects.

Let's use the ``Session`` to go to jurassicpark.wikia.com and print out the status code
and the URL::

    $session->start();

    $session->visit('http://jurassicpark.wikia.com');

    echo "Status code: ". $session->getStatusCode() . "\n";
    echo "Current URL: ". $session->getCurrentUrl() . "\n";

To try this out, run the script from the command line. Success! Behind the
scenes, this just made a *real* HTTP request to jurassicpark.wikia.com and then printed
the status code and the current URL.

The Page (DocumentElement)
--------------------------

Important object #3 is the Page, which you can get by calling ``$session->getPage()``.
If the ``Session`` is your browser, then this object represents the HTML DOM
of the current page. You could also think of it as the jQuery object, because
we'll use it to traverse down our page and find different elements.

The tricky thing with the Page is that its an instance of ``DocumentElement``,
which doesn't sound like a Page at all! When you look at its API docs, make
sure to check out all of the methods on this class as well as those it inherits
from its base classes.

The easiest thing to do with the page is get its HTML or text. Let's use
it to print out the beginning of the jurassicpark.wikia.com homepage::

    $page = $session->getPage();

    echo "First 160 chars: ".substr($page->getText() , 0, 160) . "\n";

Try the script again. Yea! We're killing it!

The NodeElement and finding via CSS
-----------------------------------

Getting the text of the page is great, but a more more powerful feature of
the page is to find and traverse elements using CSS. To do this,
use either the ``find`` method to get one element or ``findAll`` to get an
array of matched elements::

    $anchorEle = $page->find('css', 'h3 a.title');

The return value of ``find`` is a ``NodeElement``, which is our 4th and last
important object in Mink! If you look at its API documentation, you'll see
that it extends the same base class as ``DocumentElement``, which means that
it has almost all the same methods and more. For example, once you've found
a ``NodeElement``, you can find more elements deeper inside of it::

    $spanEle = $anchorElement.find('css', 'span.emph');

With all this new knowledge, let's find the sub-link beneath "On the Wiki"
and print its text::

    $element = $page->find('css', '.subnav-2 li a');

    echo "The link text is: ". $element->getText() . "\n";

When we run the file again, it prints out "Wiki Activity". And when we look at
the site, this is exactly what we expect. Awesome!

The Named Selector
------------------

So far, we've found elements by using CSS selectors. But there is one other
way to find elements, and it's *really* important, especially when we start
using Behat and Mink together. It's called the "named" selector, and it works
by trying to find elements by matching their "text". Let's use it to find
the link whose text is "Random page".

First, grab an object called the ``SelectorsHandler``. Next, use the same
``find`` method as before, but in place of ``css``, use ``named`` as the
first argument. As the second argument, pass an array with two elements:
the string ``link``, and the string ``Random page`` wrapped in an ``xpathLiteral``
function. Yep, this looks ugly, but it'll all make sense in a second!::

    $selectorsHandler = $session->getSelectorsHandler();
    $element = $page->find(
        'named',
        array(
            'link',
            $selectorsHandler->xpathLiteral('MinkExtension')
        )
    );

But first, print out the URL of the ``NodeElement`` and test that things
are actually working::

    echo sprintf(
        "The URL is '%s'\n",
        $element->getAttribute('href')
    );

The best way to understand the "named" selector is to open up the class that's
behind the magic:: ``NamedSelector``. At the top is a large array with keys
like "link", "field", and "button" and next to each is a big, ugly, but fascinating
xpath. In our ``find`` method, we asked to find a ``link`` matching ``Random page``.
This then uses the long xpath next to ``link`` to try to find a matching
element. I'm not an xpath expert, but if you look closely, you can see that
it tries to find an anchor tag whose id matches ``Random page``, or which
contains the text ``Random page``, or whose ``title`` attribute contains
``Random page``, or which contains an ``img`` tag whose ``alt`` attribute
contains ``Random page`` and even more after that. Basically, this will
find the anchor tag that matches the text in any possible way that we might imagine.
It's important because it's actually very "human" it allows us to find
elements using the same language as our users. For example, our user would never
say ``Click the anchor tag that is inside an element with class subnav-2``.
In reality, the user would say ``Click the "Random page" link``. The named
selector finds that element.

Once you understand this, you'll love what's next. Both the ``DocumentElement``
and ``NodeElement`` objects have a bunch of shortcuts to find things using
the named selector. For example, finding the ``Random page`` link is as
easy as saying ``findLink``::

    $element = $page->findLink('Random page');

Internally, this is using the "named" selector. And there are a lot more
methods just like this, such as ``findButton`` and ``findField``. As you'll
see later, the last function is especially important, because it allows you
to find fields by referring to the label for that field::

    $firstNameInput = $page->findField('First Name');

Click, Dragging, and doing other things with an element (NodeElement)
---------------------------------------------------------------------

Now that you're a master at finding elements on a page, let's do something
with them! The ``NodeElement`` object has methods for just about anything
you could ever think to do with a field, like ``click``, ``press``, ``check``
if the element is a checkbox or ``attachFile`` if it's an upload field. If
you need information, you can use methods like ``getTagName`` or ``getAttribute``.
Later, when we start testing with Selenium2, you'll even be able to do things
that rely on JavaScript like ``rightClick``, ``mouseOver``, and ``dragTo``!

Let's use this to click on the ``Random page`` link and print out the new
URL on the next page::

    $element->click();

    echo "Page URL after click: ". $session->getCurrentUrl() . "\n";

Running Tests in JavaScript
---------------------------

So far, the HTTP requests that Mink is making are done in the background
using CURL. But what if the site we're browsing relies on JavaScript? That
just wouldn't work at all right now.

To fix this, comment out the ``GoutteDriver``, and instead use the ``Selenium2Driver``::

    use Behat\Mink\Driver\GoutteDriver;
    use Behat\Mink\Driver\Selenium2Driver;

    //$driver = new GoutteDriver();
    $driver = new Selenium2Driver;

Also make sure to "stop" your Mink session at the end of your script. This
wasn't needed with Goutte, but with Selenium2, the ``start`` function opens
the browser and ``stop`` closes it::

    $session = new Session($driver);
    $session->start();

    // everything ...

    $session->stop();

Also, remove the status code line, as Selenium doesn't support getting the
page's status code.

In your terminal, start the selenium server that we downloaded earlier. And
just by changing one line of code to switch drivers, when we execute the
test it now actually opens up Selenium2 and performs our actions! This is
the reason Mink was created: sometimes your code can be run quickly in a
headless browser like Goutte and sometimes you need something that supports
JavaScript like Selenium2. Mink gives you a single, simple API that lets you
write the same code, and then choose very easily how you want that code written.
When we use Behat and Mink together, turning Selenium2 on and off is even
easier.

The Key Points to Mink
----------------------

And that's it for Mink! Remember that Mink is really just 4 important objects:
the ``Driver``, ``Session``, Page or ``DocumentElement`` and element or ``NodeElement``.
To find elements on a page, use the ``find`` or ``findAll`` methods with
either the ``css`` or ``named`` selector. Shortcut methods like ``findLink``
and ``findButton`` make it even easier to use the named selector. And once
you've found the element you need, do something with it - like calling the
``click`` method or getting its text via ``getText``.


.. _`Mink API docs`: http://mink.behat.org/api/
.. _Goutte: https://github.com/fabpot/Goutte
