--TEST--
Closure signature types: a matching closure is accepted, mismatch throws
--FILE--
<?php
function takeTwo(Closure(int, int): int $c): int {
    return $c(2, 3);
}

// Matching closure conforms.
var_dump(takeTwo(fn(int $a, int $b): int => $a + $b));

// Wrong parameter type -> TypeError.
try {
    takeTwo(fn(string $a, string $b): string => $a . $b);
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
int(5)
takeTwo(): Argument #1 ($c) must be of type Closure(int, int): int, signature Closure(string, string): string given
