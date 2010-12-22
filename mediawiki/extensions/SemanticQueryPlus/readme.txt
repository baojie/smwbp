
==What This Extension Does==

The SemanticQueryPlus extension  ("SMW-QL+")  is an extension of the [http://semantic-mediawiki.org Semantic MediaWiki] extension. It extends the modeling (SMW-ML) and query language (SMW-QL) of SMW with
* negation and cardinality in query
* inverse property in query and modeling
* transitive, functional, inverse functional, symmetric properties in modeling
* domain and range inference for properties in modeling

It is based on a theoretical work described in 

''Jie Bao, Li Ding, James A. Hendler. Knowledge Representation and Query in Semantic MediaWiki: A Formal Study, In Tetherless World Constellation (RPI) Technical Report, pp. TW-2008-42, 2008
http://tw.rpi.edu/wiki/TW-2008-42''

==How It Works==

The extension translates both the SMW semantic markup ("the modeling language") and the query language into logic programs (LP), and uses a LP solver as the reasoner. For this implementation, we used [http://www.dbai.tuwien.ac.at/proj/dlv/ dlv] as the reasoner, but other LP solvers may be used as well.

It has two work modes
* '''file-based mode''': first, the administrator need to build a dump of the wiki semantic data using "php QLPlus_dump.php". In this mode, no real-time change will be captured in queries.
* '''database-based mode''': the wiki semantic data may also be dumped to a shadow database via ODBC. Real-time changes of instance data will be updated, but ontological changes (i.e. the ones about categories and properties, or the introduction of new category/property) won't. See [http://www.dlvsystem.com/dlvsystem/html/DLV_User_Manual.html#ODBC DLV ODBC setup] for setup.

=== Scalability ===

Tested on a machine with configuration: 2 * Xeon 5365 Quad 3.0GHz 1333MHz /16G / 2 * 1TB

A dump of part of DBLP data is used, with about 10k pages and 100k triples.

For the file-based mode, most of queries is answered <1.5s. 

For the database-based mode, most of queries is answered <2.5s. In general, db-mode takes 50% more time than the file-base mode.

The execution time is linear to the size of the wiki, and almost constant for most of queries (due to the nature of modeling building strategies used in lp solvers).

=== Caching ===

If caching is turned on ($wgQLPlus_UseCache = true), then a query is only executed for the first visit, following page visits will be loaded from a cache. The cache may be refreshed by the "refresh" (purge) action.

==Semantic Query Plus Syntax==

Use the "askplus" hook function: e.g.
<pre>
{{#askplus:
   [[Category:C]]
   |limit=10
   |format=table
}}
</pre>
The syntax is an extension of SMW-QL, so you can use most of SMW-QL features here (some limitations apply)

===Features inherited from SMW===

From [[http://semantic-mediawiki.org/wiki/Help:Properties_and_types SMW-ML]]
* category instantiation e.g. <nowiki>[[Category:C]]</nowiki>
* property instantiation e.g. <nowiki>[[Property:P]]</nowiki>
* subclass, e.g. <nowiki>[[Category:C]]</nowiki> (on a category page)
* subclass, e.g. <nowiki>[[Subpropety of:Property:P]]</nowiki> (on a property page)

From [http://semantic-mediawiki.org/wiki/Help:Selecting_pages SMW-QL]
* conjunction: e.g., <nowiki>[[Category:C]][[P::v]]</nowiki>
* disjunction: e.g., <nowiki>[[Category:C]] or [[P::v]]</nowiki>, <nowiki>[[A||B]] or [[P::v||w]]</nowiki>
* property chain: e.g., <nowiki>[[P.Q::v]]</nowiki>
* property wildcat: e.g., <nowiki>[[P::+]]</nowiki> 
* subquery: e.g., <nowiki>[[P::<q>[[Category:C]]</q>]]</nowiki>
* inverse property e.g., <nowiki>[[-P::v]]</nowiki>
* value comparison, e.g. <nowiki>[[P::>3]][[P::<7]][[P::!5]]</nowiki>

===Negation===
Use '<>' before a category or property's name

Example
<pre>
{{#askplus:
  [[<>Category:C]]
  [[Category:D]]
}}
</pre>
Instances of D which are not instances of C.

<pre>
{{#askplus:
  [[Category:C]]
  [[<>P::+]]
}}
</pre>
Instances C that have no attribute value of P

It's always a good idea to not use a negated query condition alone (e.g., {{#askplus:  [[<>Category:C]]}}), because it may lead to a VERY large result set.

===Cardinality===
Unqualified cardinality queries:
<pre>
{{#askplus:
  [[>=3#P::+]]
}}
</pre>
Find instances with at least 3 attribute values of P

Qualified cardinality queries:
<pre>
{{#askplus:
  [[<3#P::<q>[[Category:D]]</q>]]
}}
</pre>
Find instances with less than 3 attribute values of P, which are instances of D

===Domain and Range===

On a property page. e.g. Property:P, add

<pre>
[[Domain:Category:C]]
[[Range:Category:D]]
</pre>
means that for every instance <nowiki>[[P::y]]</nowiki> on page x, then x is an instance of C, y is an instance of D.

The "Category:" prefix may be omitted. Thus, the following script has the same effect.

<pre>
[[Domain:C]]
[[Range:D]]
</pre>

===Property Types===

On Property:P, one may declare

<pre>
[[Type::Transitive]]
[[Type::Symmetric]]
[[Type::Functional]]
[[Type::InverseFunctional]]
</pre>

For properties of Functional or InverseFunctional types, "SameAs" relations maybe inferred. For instance, for a functional property P, with "<nowiki>[[P::v1]][[P::v2]]</nowiki>" on the same page, then SameAs(v1,v2) is inferred (i.e., equivalent to adding <nowiki>[[SameAs::v2]]</nowiki> to page v1).

While it is possible to add those markup to any properties, to ensure correct inference, it's better only add them to properties of "Page" datatypes, i.e. the ones with <nowiki>[[Has type::Type:Page]]</nowiki> (this is default type of a property)

==Configuration==

To be added. See QLPlus_Setting.php for details.

==Demo and Examples==

To be set up

===Model Integrity Constraint===

The SMW-QL+ language maybe used to state integrity constraints. For instance, if we require every person to have a name, the following query will find all instances violating this constraint 
<pre>
{{#askplus:
  [[Category:Person]]
  [[<>Has name::+]]
}}
</pre>
Combining this with templates, it's easy to show integrity constraint warnings on a category page, or on individual pages that violates the constraint.

==Installation and Maintenance==

Download the zip, unzip to /extension/SemanticQueryPlus. Add to LocalSetting.php one line

require_once("$IP/extensions/SemanticQueryPlus/QLPlus_AskQueryExtension.php");

Make sure executables under /bin have the right permissions (e.g., 755)

==Dumping Data==

Use "php QLPlus_dump.php" under the extension's folder. By default, the dumped data is stored under /dump folder. If the database-mode is selected, some mapping information and the ontological data are still saved in that folder, not in the database.

It's advised to periodically re-dump the data. For moderate-sized wikis (<100k triples) and mainstream servers configuration, the process should be less than 5 minutes.

==Limitations==

Not all printing features of the "ask" function are supported, e.g. counting, soting or multi-page viewing. [To be extended]

==Change Log==

the latest SemanticHistory has been tested on MediaWiki versions 1.16 and Semantic MediaWiki version 1.5.4. 

'''History:'''
* Dec 22, 2010 version 0.1 -first release