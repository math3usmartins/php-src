--TEST--
Closure signature types: single class-typed parameter/return variance is enforced
--FILE--
<?php
class A {}
class B extends A {}
class Unrelated {}

// Contravariant parameter: a wider candidate param is accepted; a narrower or
// unrelated one is rejected.
function pTake(Closure(B): int $c): int { return $c(new B()); }
var_dump(pTake(fn(A $x): int => 1));   // A is wider than B -> OK
var_dump(pTake(fn(B $x): int => 2));   // exact -> OK
try {
    pTake(fn(Unrelated $x): int => 3);
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Covariant return: a narrower (or equal) candidate return is accepted; an
// unrelated one is rejected.
function rTake(Closure(): A $c): A { return $c(); }
var_dump(rTake(fn(): B => new B()) instanceof A);  // B is narrower than A -> OK
var_dump(rTake(fn(): A => new A()) instanceof A);  // exact -> OK
try {
    rTake(fn(): Unrelated => new Unrelated());
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Interfaces resolve the same way.
function iTake(Closure(Iterator): int $c): int { return 0; }
iTake(fn(Traversable $x): int => 1);   // Traversable wider than Iterator -> OK
try {
    iTake(fn(ArrayIterator $x): int => 1);  // narrower -> rejected
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}
echo "ok\n";
?>
--EXPECT--
int(1)
int(2)
pTake(): Argument #1 ($c) must be of type Closure(B): int, signature Closure(Unrelated): int given
bool(true)
bool(true)
rTake(): Argument #1 ($c) must be of type Closure(): A, signature Closure(): Unrelated given
iTake(): Argument #1 ($c) must be of type Closure(Iterator): int, signature Closure(ArrayIterator): int given
ok
