<?php 

require_once(dirname( __FILE__ ). "/../QLPlus_Rule.php");

function testAtom()
{
	$a = new Atom("loves",array("X","Y"),true);
	print $a->toString();
}

function testRule()
{
	$h = new Atom("a",array("X"));
	$b = new Atom("b",array("X"));
	$r = new Rule($h,array($b));
	print $r->toString();
}

function testSimplify()
{
	$a = new Atom("a",array("X"));
	$b = new Atom("b",array("X")); 
	$b1 = $b->copy();
	$b->is_naf = true;	
	$c = new Atom("c",array("X"));
	$d = new Atom("d",array("X"));
	
	$aw = new Atom("a",array("w"));
	
	$r0 = new Rule($aw, null);
	$r1 = new Rule($a,array($b));
	$r2 = new Rule($b1,array($c));
	$r3 = new Rule($d,array($a,$b));
	
	$lp = new LogicProgram (array($r0,$r1,$r2,$r3));
	print $lp->toString();
	
	print "\nAfter\n\n";
	$exception = "a";
	$lp->simplify($exception);
	print $lp->toString();	
}

print "Test the \"Rule\" class\n";
//testAtom();
//testRule();
testSimplify();
?>