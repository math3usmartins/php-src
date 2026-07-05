--TEST--
Closure signature types: variadic and optional parameter arity rules
--FILE--
<?php
// A closure may declare EXTRA optional/variadic params and still conform.
function apply(Closure(int): int $c): int {
    return $c(10);
}

var_dump(apply(fn(int $x, int $y = 0): int => $x + $y)); // extra optional: OK
var_dump(apply(fn(int ...$xs): int => array_sum($xs)));  // variadic: OK

// But requiring MORE than the signature supplies must fail.
try {
    apply(fn(int $x, int $y): int => $x + $y);
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
int(10)
int(10)
apply(): Argument #1 ($c) must be of type Closure(int): int, signature Closure(int, int): int given
