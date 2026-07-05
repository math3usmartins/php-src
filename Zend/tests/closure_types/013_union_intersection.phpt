--TEST--
Closure signature types: class union/intersection member variance is enforced
--FILE--
<?php
interface I1 {}
interface I2 {}
class A implements I1 {}
class B implements I1, I2 {}
class Unrelated {}

// Contravariant union parameter: the candidate param must accept everything the
// signature's union can supply, i.e. be a supertype of (A|B).
function takeUnion(Closure(A|B): int $c): string { return "ok"; }
echo takeUnion(fn(I1 $x): int => 1), "\n";              // A|B both <: I1 -> accept
try {
    takeUnion(fn(A $x): int => 1);                       // B is not <: A -> reject
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Contravariant intersection parameter: (I1&I2) is at least as narrow as either
// member, so a candidate accepting I1 (or I2) alone conforms.
function takeInter(Closure(I1&I2): int $c): string { return "ok"; }
echo takeInter(fn(I1 $x): int => 1), "\n";              // I1&I2 <: I1 -> accept
try {
    takeInter(fn(Unrelated $x): int => 1);               // neither member <: Unrelated
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Intersection-vs-intersection must allow a "split": different candidate
// members may satisfy different signature members.
interface Ia extends I1 {}   // Ia <: I1, not <: I2
interface Ib extends I2 {}   // Ib <: I2, not <: I1
interface Ic {}              // unrelated to I1 and I2
class AB implements Ia, Ib {}
function retInter(Closure(): I1&I2 $c): string { return "ok"; }
echo retInter(fn(): Ia&Ib => new AB()), "\n";   // Ia<:I1, Ib<:I2 (split) -> accept
try {
    retInter(fn(): Ia&Ic => new AB());            // nothing <: I2 -> reject
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

// Covariant union return: the candidate return must be a subtype of (A|B).
function retUnion(Closure(): A|B $c): string { return "ok"; }
echo retUnion(fn(): A => new A()), "\n";               // A <: A|B -> accept
try {
    retUnion(fn(): Unrelated => new Unrelated());       // Unrelated not in A|B -> reject
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

echo "done\n";
?>
--EXPECT--
ok
takeUnion(): Argument #1 ($c) must be of type Closure(A|B): int, signature Closure(A): int given
ok
takeInter(): Argument #1 ($c) must be of type Closure(I1&I2): int, signature Closure(Unrelated): int given
ok
retInter(): Argument #1 ($c) must be of type Closure(): I1&I2, signature Closure(): Ia&Ic given
ok
retUnion(): Argument #1 ($c) must be of type Closure(): A|B, signature Closure(): Unrelated given
done
