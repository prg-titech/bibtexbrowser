<?php
/*
  Bibliography index generator

  This page generates a list of bibliography entries with jump icons
  to the detailed sections outside of this page.  It needs the
  following parameters:

  bib:     a semicolon separated list of .bib files

  keys:    a comma separated list of bibtex keys

  parent: an URL of the page that embeds this page, which should have
          key anchors.  When this parameter is ommitted, no jump icons
          are generated.

  Example: assume https://mybib.com/index-gen.php is the URL of this
  generator.  A page at https://mybib.com/news.html can use the
  generator in this way (the long URL is folded for readability here):

  <iframe src="https://mybib.com/index-gen.php?
               bib=mybib.bib;theirs.bib&
               keys=oopsla1992,popl1988&
               parent=/news.html" />
  ... 
  <div id="oopsla1992">
     details of the entry
  </div>
  <div id="popl1988">
     details of the entry
  </div>

  as this page generates the following content:

  <ul>
   <li>Next 700 Problems in Object-Oriented Programming (John Doe) 
       <a href="/news.html#oopsla1992" target="_parent">...</a></li>
   <li>Next 700 Principles of Programming (Alissa P Hacker) 
       <a href="/news.html#popl1998" target="_parent">...</a></li>
  </ul>
 */

// specify entry formats
define('BIBLIOGRAPHYSTYLE','TitleAuthorBibliographyStyle');
define('BIBTEXBROWSER_LINK_STYLE','JumpToDetailsBib2Links');
define('BIBTEXBROWSER_BIBTEX_LINKS',false); // no [bibtex] link by default

// show only a title and authors 
function TitleAuthorBibliographyStyle(&$bibentry) {
    return $bibentry->getTitle().' ('.$bibentry->formattedAuthors().')';
}

// a jump button to details, provided outside of this page
function JumpToDetailsBib2Links(&$bibentry) {
    global $parent;
    if ($parent) {
        $result = "<a href='{$parent}#{$bibentry->getKey()}' target='_top'>" .
                "&#x1f5d0;</a>";
        return $result;
    } else return '';
}

$_GET['library']=1;
require_once('bibtexbrowser.php');
global $db;
$db = new BibDataBase();
// load .bib files specified in the bib parameter
foreach (explode(";", $_GET['bib']) as $bib) {
    $db->load($bib);
}

global $parent;
$parent = $_GET['parent'];

echo "<ul>";

// process each key in the keys parameter
foreach (explode(",", $_GET['keys']) as $key) {
    $query = array(Q_KEY=>$key);
    $entries=$db->multisearch($query);
    $count = count($entries);
    if ($count != 1) {
        echo "<li>Expected one entry for {$key}, but found {$count} entries.</li>";
        continue;
    }
    // uasort($entries, 'compare_bib_entries');
    // foreach ($entries as $bibentry) {
    $bibentry = $entries[0];
    $ent = $bibentry->toHTML();
    echo "<li>{$ent}</li>";
    // }
}
echo "</ul>";
    ?>
