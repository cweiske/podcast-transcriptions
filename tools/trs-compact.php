<?php
/**
 * Compact .trs files:
 * - Remove "no speaker" turns (extend previous turn)
 * - Remove turns without text content (extend previous turn)
 * - Combine following turns that have the same speaker
 *
 * Limitations:
 * - Works on one episode only
 */
if ($argc < 2) {
    echo "Parameters missing: transcription.trs\n";
    exit(1);
}
$fileTrsIn = $argv[1];

if (!file_exists($fileTrsIn)) {
    echo ".trs file does not exist\n";
    exit(1);
}

//We use DomDocument instead of SimpleXML because SimpleXML has no way
// to handle text nodes
$domOut = new DomDocument();
$domOut->load($fileTrsIn);
$domOut->formatOutput = true;
$xpath = new DomXPath($domOut);

$lastTurn = null;
$toRemove = [];

foreach ($xpath->query('//Episode/Section/Turn') as $turn) {
    if ($lastTurn === null) {
        $lastTurn = $turn;
        continue;
    }

    //turn without text
    $text = trim($turn->textContent);
    if ($text === '') {
        $lastTurn->setAttribute('endTime', $turn->getAttribute('endTime'));
        $toRemove[] = $turn;
        continue;
    }

    //same speaker - copy contents over
    if ($lastTurn->getAttribute('speaker') == $turn->getAttribute('speaker')) {
        $lastTurn->setAttribute('endTime', $turn->getAttribute('endTime'));
        $toRemove[] = $turn;
        //the next() implementation of childNodes probably works
        // with nextSibling, which breaks as soon as we move the first
        // child
        $children = [];
        foreach ($turn->childNodes as $child) {
            $children[] = $child;
        }
        foreach ($children as $child) {
            $lastTurn->appendChild($child);
        }
        continue;
    }

    $lastTurn = $turn;
}
echo "--\n";

foreach ($toRemove as $element) {
    $element->parentNode->removeChild($element);
}

echo $domOut->saveXML();
?>
