--TEST--
Closure signature types: return-statement position is enforced
--FILE--
<?php
// A conforming closure may be returned.
function make(): Closure(int): int {
    return fn(int $x): int => $x;
}
var_dump(make() instanceof Closure);

// A non-conforming closure (return covariance fails) throws on return.
function bad(): Closure(int): int {
    return fn(string $s): string => $s;
}
try {
    bad();
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// A non-Closure object is rejected too.
function notClosure(): Closure(int): int {
    return new stdClass();
}
try {
    notClosure();
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
bool(true)
bad(): Return value must be of type Closure(int): int, signature Closure(string): string returned
notClosure(): Return value must be of type Closure(int): int, stdClass returned
