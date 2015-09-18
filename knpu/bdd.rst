Behavior-Driven Development
===========================

Once upon a time, you probably didn't write any tests. We know, we've been
there, it's not a happy place, but we all start somewhere. Eventually, you
started writing some tests, and even read about or tried "Test-Driven Development".
The idea is simple, write your tests first, and *then* write code until your
tests pass. It helps clarify your goals before you spend time developing.
You also know that as soon as your tests all pass, you should stop working!
This makes it harder to over-perfect your code.

Behavior-driven development, or BDD, is the next evolution. It's similar to
TDD except that instead of writing tests first, we'll create written descriptions
of the *behavior* of a feature. Dan North - the father of all of this
BDD stuff - once wrote that `'Behaviour' is a more useful word than 'test'`_.
Of course! Writing tests is great, but before we do any work, we need to understand
the exact *behavior* of the feature we're building.

There are two styles of BDD - SpecBDD and StoryBDD. Roughly speaking, Spec
is used for writing unit tests. In PHP, a wonderful library called `PHPSpec`_
exists to help with this style. In this course, we're talking about the other
type, StoryBDD, which is typically used for functional testing. In an ideal
world, you'll use both styles.

If nothing else, BDD aims to solve the great problem of communication. Every
project has a lot of players: developers, a client, project managers, velociraptors,
pterodactyl and Samuel L. Jackson. As computer scientists, the development process
is usually a bit of a `goat rodeo`_, where nobody really understands the full goals
or behavior of what's being built.

BDD aims to fix this by giving us a standard language for describing these
features. We'll also follow a workflow that will help make the whole process
from "great idea" to development much more sane:

1. **Define** the business value of the features
2. **Prioritize** features by their business value
3. **Describe** them with readable scenarios
4. And only then - **implement** them

As a developer, this might look like boring business-talk. But I've used this
countless times to break a big idea into smaller pieces written in clear language. 
And for a developer, getting clear directions rocks!

Ok, all the theory is behind us, let's get to Gherkin, the language of BDD!

.. _`'Behaviour' is a more useful word than 'test'`: http://dannorth.net/introducing-bdd/
.. _`PHPSpec`: http://www.phpspec.net/
.. _`Chinese whispers`: http://en.wikipedia.org/wiki/Chinese_whispers
.. _`goat rodeo`: http://www.urbandictionary.com/define.php?term=goat+rodeo