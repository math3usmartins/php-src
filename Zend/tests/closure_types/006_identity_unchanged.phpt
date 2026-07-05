--TEST--
Closure signature types: a signed closure is still just a \Closure (no nominal type)
--FILE--
<?php
function takeOne(Closure(int): int $c): Closure {
    // Identity / instanceof semantics are deliberately unchanged.
    var_dump($c instanceof Closure);
    var_dump(get_class($c));
    return $c;
}

takeOne(fn(int $x): int => $x);
?>
--EXPECT--
bool(true)
string(7) "Closure"
