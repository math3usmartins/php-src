--TEST--
Closure signature types: return type is covariant; conflicting return throws
--FILE--
<?php
function makeProducer(Closure(): int $c): int {
    return $c();
}

var_dump(makeProducer(fn(): int => 42));

try {
    makeProducer(fn(): string => "nope");
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
int(42)
makeProducer(): Argument #1 ($c) must be of type Closure(): int, signature Closure(): string given
