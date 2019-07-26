## Task management system

[![Build Status](https://travis-ci.org/mapogolions/cooperative-multitasking.svg?branch=master)](https://travis-ci.org/mapogolions/cooperative-multitasking)

### Key points to keep in mind

* likewise 1 Core CPU [Flynn's taxonomy](https://en.wikipedia.org/wiki/Flynn%27s_taxonomy)
* single thread
* event loop
* tasks are generators/coroutines
* cooperative multitasking

### Definitions

__Cooperative multitasking__ - tasks yield to scheduler.

__Preemptive multitasking__ - scheduler interrupts tasks.



### How to use

```sh
> git clone ...
> cd project
> php composer.phar install / composer install
> ./vendor/bin/phpunit
```

then copy any snippet (snippets directory) and paste to the `App.php` file.

```sh
> php -f App.php
```

### Further information

* [A Curious Course on Coroutines and Concurrency](http://dabeaz.com/coroutines/)
* [Miguel Grinberg Asynchronous Python for the Complete Beginner PyCon 2017](https://www.youtube.com/watch?v=iG6fr81xHKA)
