<?php 
require_once(dirname( __FILE__ ) . "/../QLPlus_Setting.php");
require_once(dirname( __FILE__ ) . "/../QLPlus_AskQuery.php");

function test()
{
	$query = new AskQuery();
	$a = new AtomSelector();$a->setPage(":Category:A");
	$b = new AtomSelector();$b->setPage("Concept:B");
	$c = new AtomSelector();$c->setPage("Krötzsch");
	$d = new AtomSelector();$d->setPage("Category:D");$d->isNaf=true;
	
	//E>=v1
	$cond = new Condition(); $cond->atom = "v1";  $cond->modifier=">";
	$e = new AtomSelector();$e->setPropetyCondition("E",array($cond));
	
	//[[Category:F]]
	$f = new AtomSelector();$f->setPage("Category:F");
	//[[Category:F]] or [[E>=v1]]
	$sel2 = new Seletors(array(array($f),array($e)));	
	$cond2 = new Condition(); $cond2->isSubquery = true; $cond2->seletors = $sel2;	
	// G::<q>[[Category:F]] or [[E>=v1]]</q>	
	$g = new AtomSelector();$g->setPropetyCondition("G",array($cond2));
	
	//[[a]][[b]] or [[c]][[d]]
	//$sel = new Seletors(array(array($a,$b),array($c,$d)));	
	
	// G::<q>[[Category:F]] or [[E>=v1]]</q>	
	//$sel = new Seletors(array(array($g)));	
	
	//[[<>p::+]]
	$cond_p = new Condition(); $cond_p->atom = "+";
	$p = new AtomSelector() ; $p->setPropetyCondition("P.Q",array($cond_p));$p->isNaf=true;	
	
	$sel = new Seletors(array(array($p)));	
	//print_r($sel);
	
	$query->selectors = $sel; 
		
	print("\nTranslated LP\n\n");
	// translate to LP and do simplification.	
	$query->toLP(true);
	//$query->toLP(false);
	//print $query->rules->toString();
	//println("\n---Simplifed----\n");
	//$query->rules->simplify("tmp_result");
	print $query->rules->toString();
	//$query->toPrint();	
}

test();

?>