--TEST--
Closure signature types: usable as property and return types
--FILE--
<?php
class Calc {
    public Closure(int, int): int $op;

    public function __construct() {
        $this->op = fn(int $a, int $b): int => $a * $b;
    }

    public function operator(): Closure(int, int): int {
        return $this->op;
    }
}

$c = new Calc();
$op = $c->operator();
var_dump($op(6, 7));

try {
    $c->op = fn(int $a): int => $a; // wrong arity
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
int(42)
Cannot assign Closure with signature Closure(int): int to property Calc::$op of type Closure(int, int): int
