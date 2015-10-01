# Mink

I mentioned that there are two libraries that we would be working with. 
Behat, which is all about reading the steps and executing these functions
and Mink. Mink is the work horse that's able to browse websites, fill out
forms, click on links and do other cool stuff like that. 

This is such a cool library that it deserves some of our attention. Start
by creating a `mink.php` file right at the root of your project. And require
composer's autoload file. We'll use Mink all by itself, outside of Symfony,
Behat and everything just to see how it works. 

As awesome as Mink is, it only has four important objects. The first of these
objects is the driver. Create a new driver and set it to `$new GoutteDriver();`.
I'm not even going to get into this yet, so hold on, we'll get to that later.

Moving on the the second important object, and the first that we really care about,
which is the session. `$session = $new Session();` and pass it the `$driver` as
an argument. 

Think of a session like a browser tab, anything you can do in a tab you can do
in a session. When you think about it, that isn't very much. You can visit URLs,
refresh, go backwards or forwards and that's about it. So let's use it to visit
a very awesome and absurdly designed site jurassicpark.wikia.com. After that we'll
just print out a few things about the page like the status code, and the current URL.
Awesome!
