### Task management system

1) based on generators/coroutines
2) only one thread
3) cooperative multitasking

__Cooperative multitasking__ - tasks yield to scheduler.
__Preemptive multitasking__ - scheduler interrupts tasks.

An insight:

* `yield` statement is a kind of *trap*

* when generator function hits a `yield` statement, it immediatly suspends execution

* control is passed back to whatever code make the generator function run (unseen) 

* if you treat yield as a trap, you can build a multitasking "operating system" -- all in PHP!