--TEST--
Closure signature types: untyped closure params are mixed (top) and always conform
--FILE--
<?php
function takeTwo(Closure(int, int): int $c): int {
    return $c(4, 5);
}

// No annotations on the closure: untyped params are `mixed`, which is wider
// than int (contravariant), so this conforms WITHOUT inference.
var_dump(takeTwo(fn($a, $b) => $a + $b));
?>
--EXPECT--
int(9)
