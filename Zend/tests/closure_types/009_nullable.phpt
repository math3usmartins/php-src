--TEST--
Closure signature types: nullable ?Closure(...) accepts null and still enforces
--FILE--
<?php
// Nullable parameter: null and a conforming closure are accepted; a
// non-conforming closure is still rejected.
function f(?Closure(int): int $c): ?Closure(int): int {
    return $c;
}
var_dump(f(null));
var_dump(f(fn(int $x): int => $x) instanceof Closure);
try {
    f(fn(string $s): string => $s);
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Nullable typed property.
class C {
    public ?Closure(int): int $p = null;
}
$o = new C();
$o->p = null;
$o->p = fn(int $x): int => $x;
var_dump($o->p instanceof Closure);
try {
    $o->p = fn(string $s): string => $s;
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Nullable return accepts null.
function g(): ?Closure(int): int {
    return null;
}
var_dump(g());
?>
--EXPECT--
NULL
bool(true)
f(): Argument #1 ($c) must be of type ?Closure(int): int, signature Closure(string): string given
bool(true)
Cannot assign Closure with signature Closure(string): string to property C::$p of type ?Closure(int): int
NULL
