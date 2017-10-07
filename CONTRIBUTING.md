## Coding standard of Edde Framework
Rules below are mandatory; I'll expand/update list based on current work. If you want contribute, you must follow these rules.

Yes, discussion is allowed, but if some change is not approved and is not listed here, you must still follow current rules.

- tabs only [1]
- utf-8 charset
- use strict comparsion (===, !==, ...) [2]
- false, true, null, ... lowercase
- braces on the same line (`function() { ..., class ... { ....`)
- object typing where it can be; Edde is PHP 7, so return hints and scalar hints are mandatory
- long names named after object name, e.g. `MyUltimateClass` will have `$myUltimateClass` property/variable/... [3]
- minimal usage of "else"; if "if" can be inlined into return, do it (`if(true) return true; else return fale;` - nope) [4]
- shorthand arrays []
- doc comments only within interfaces [5]
- protected properties almost everywhere; public properties are strictly **forbidden** [6]
- usage of `__destruct()` is strictly **forbidden**
- use "use" clause to import classes; except root classes e.g. \ReflectionClass, ...
- constants usage - forbidden; there are really _**rare**_ usecases [7]
- no (logic, executive) code in constructor; use lazy approach [8]
- no deep chaining, e.g. `$this->getA()->getB()->getC()[->doSomething()]`; you have Dependency Injection for this
- abstract class MUST have `Abstract` prefix [9]
- interface MUST have `I` letter prefix [10]
- traits MUST have `Trait` appendix
- when method return exactly bool in `if` statement, use explicit `=== false` for false check, omit `=== true` for true check

---
[1] this is holy war; I'll not describe "why"

[2] there are some exceptions; primary purpose is to be a little bit more type safer

[3] talkative, but you know with you are working on a first look

[4] only code "compression" purpose; some times harder to read, but this code is not tutorial

[5] for easier updating (and prevent outdating) do not use doc comments over concrete class implementations

[6] this will ensure simple inheritance without pain

[7] constant break public API - "user" must know, how to work with those constants (e.g. you have database model and you wanna test if it is new - there is no `Model::isNew()` method, only `Model::getState()` and `Mode::STATE_NEW` -> `Model::getState() === Model::STATE_NEW`, ugly, isn't it?

[8] if there is logic in constructor class is heavyweight and cannot be precisly controlled outside (and so instantiation is "harder")

[9] originally I was against this convention, but when I commonly met naming situations in code, I've been forced to use this long naming

[10] IMO I prefix is usable (better) than appending *Interface where a developer is forced to read a whole class name (which can be really long) and determine it is an interface
