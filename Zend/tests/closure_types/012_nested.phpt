--TEST--
Closure signature types: nested signatures are structurally checked
--FILE--
<?php
// A parameter that is itself a closure signature is compared structurally
// rather than as a plain object: the inner params are contravariant and the
// inner return is covariant, recursively.
function take(Closure(Closure(int): int): int $c): int {
    return $c(fn(int $x): int => $x + 1);
}

// Exact nested signature conforms.
var_dump(take(fn(Closure(int): int $g): int => $g(41)));   // 42

// Inner return mismatch: the candidate's inner closure returns string, not int.
try {
    take(fn(Closure(int): string $g): int => 0);
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Inner parameter mismatch: the candidate's inner closure takes string, not int.
try {
    take(fn(Closure(string): int $g): int => 0);
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Nesting also flows through a covariant return position.
function make(Closure(): Closure(int): int $c): int {
    $inner = $c();
    return $inner(10);
}
var_dump(make(fn(): Closure(int): int => fn(int $x): int => $x * 2));  // 20

try {
    make(fn(): Closure(int): string => fn(int $x): string => "x");
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Deep nesting stays sound (and terminates): three levels, exact, conforms.
function deep(Closure(Closure(Closure(int): int): int): int $c): int {
    return 0;
}
deep(fn(Closure(Closure(int): int): int $g): int => 0);
echo "deep ok\n";

echo "ok\n";
?>
--EXPECT--
int(42)
take(): Argument #1 ($c) must be of type Closure(Closure(int): int): int, signature Closure(Closure(int): string): int given
take(): Argument #1 ($c) must be of type Closure(Closure(int): int): int, signature Closure(Closure(string): int): int given
int(20)
make(): Argument #1 ($c) must be of type Closure(): Closure(int): int, signature Closure(): Closure(int): string given
deep ok
ok
