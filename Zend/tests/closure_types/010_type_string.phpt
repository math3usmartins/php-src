--TEST--
Closure signature types: rendered as Closure(...) in type errors and reflection
--FILE--
<?php
// A non-Closure scalar reports the real signature, not "object".
function f(Closure(int): int $c) {}
try {
    f(5);
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Reflection __toString shows the signature; nullable keeps the '?'.
function g(Closure(int, string): bool $c): ?Closure(): int {}
$r = new ReflectionFunction('g');
echo (string) $r->getParameters()[0]->getType(), "\n";
echo (string) $r->getReturnType(), "\n";

// A nullable slot renders the '?' consistently in the enforcement message too
// (the gate and the ordinary type-error path use the same renderer).
function h(?Closure(int): int $c) {}
try {
    h(fn(string $s): string => $s);
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECTF--
f(): Argument #1 ($c) must be of type Closure(int): int, int given, called in %s on line %d
Closure(int, string): bool
?Closure(): int
h(): Argument #1 ($c) must be of type ?Closure(int): int, signature Closure(string): string given
