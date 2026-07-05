--TEST--
Closure signature types: first-class callables conform structurally
--FILE--
<?php
function takeOne(Closure(string): int $c): int {
    return $c("hello");
}

// strlen(...) is a real Closure with an introspectable signature.
var_dump(takeOne(strlen(...)));
?>
--EXPECT--
int(5)
