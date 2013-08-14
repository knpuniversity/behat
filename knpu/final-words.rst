Get Testing!
============

I hope you're really excited to start using Behat and Mink in your project,
we *love* to use it in ours.

Note on testing APIs
--------------------

Behat is typically used to test websites, but
it can also be used very well to test against your API's. If you want more
details, see the `WebApiContext`_ class, which holds a lot of step definitions
for testing your API.

Continuous Integration
----------------------

One common question is: "Can I use Behat and Mink on my continuous integration
server?". The answer is absolutely yes! In the last chapter, we talked about
the ``junit`` format, which you can use to hook into your CI system. Beside
this, there are a number of things you'll need to think about.

.. tip::

    We now have even more information on continuous integration, specifically
    with Travis CI: :doc:`/screencast/question-answer-day/travis-ci`.

First, you'll need a real database to be setup and configured for your application.

Second, your application needs to be web accessible on your CI server. Remember
that Mink operates by making real HTTP requests, so your application needs
to be configured with your web server so that you can at least make local
requests to real URLs.

Finally, if you're using Selenium you'll need to have the Selenium server
running and a utility called ``xvfb`` running. On a server, you don't really
have a GUI, which means that you can't really open a browser. With ``xvfb``,
which stands for X virtual frame buffer, you can run a browser as if you
were on any computer. The exact setup will vary, but remember to start ``xvfb``
and export your display:

.. code-block:: bash

    sh -e /etc/init.d/xvfb start
    export DISPLAY=:99.0

Until Next Time
---------------

Ok, start testing! And we'll see you next time!

.. _WebApiContext: https://github.com/Behat/CommonContexts/blob/master/Behat/CommonContexts/WebApiContext.php