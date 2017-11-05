<?php
/**
 * Convert a spokendata diarization XML file to a
 * transcriber .trs file (also XML)
 *
 * Problems:
 * - endtime of one turn does not match with starttime of next turn.
 *   transcriber inserts empty (no speaker) segments.
 */
if ($argc < 2) {
    echo "Parameters missing: spokendata-diarization.xml\n";
    exit(1);
}
$fileDiarization = $argv[1];

if (!file_exists($fileDiarization)) {
    echo "Diarization file does not exist\n";
    exit(1);
}

$xmlInput = simplexml_load_file($fileDiarization);

$speakers = [];
$totalEnd = 0;
foreach ($xmlInput->segment as $segment) {
    $speakers[(string) $segment->speaker] = true;
    $totalEnd = (float) $segment->end;
}

$tpl = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE Trans SYSTEM "trans-14.dtd">
<Trans scribe="Christian Weiske" audio_filename="" version="1" version_date="171102">
 <Speakers>
 </Speakers>
 <Episode>
  <Section type="report" startTime="0" endTime="0">
  </Section>
 </Episode>
</Trans>
XML;

//We use DomDocument instead of SimpleXML because SimpleXML has no way
// to add text nodes - see https://bugs.php.net/bug.php?id=64440
$domOut = new DomDocument();
$domOut->loadXML($tpl);
$domOut->formatOutput = true;
$xpath = new DomXPath($domOut);

$domSpks = $xpath->query('//Speakers')[0];
foreach ($speakers as $name => &$id) {
    $id = 'spk' . ucfirst($name);
    $domSpk = $domOut->createElement('Speaker');
    $domSpk->setAttribute('id', $id);
    $domSpk->setAttribute('name', $name);
    $domSpk->setAttribute('check', 'no');
    $domSpk->setAttribute('type', 'male');
    $domSpk->setAttribute('dialect', 'native');
    $domSpk->setAttribute('accent', '');
    $domSpk->setAttribute('scope', 'local');
    $domSpks->appendChild($domSpk);
}

$domEpisode = $xpath->query('//Episode')[0];
$domSection = $xpath->query('//Episode/Section')[0];
$domSection->setAttribute('endTime', $totalEnd);

$lastSpeaker = null;
$domTurn = null;
foreach ($xmlInput->segment as $segment) {
    $speaker = (string) $segment->speaker;
    $start   = (float) $segment->start;
    $end     = (float) $segment->end;

    if ($speaker != $lastSpeaker) {
        if ($domTurn !== null) {
            $domTurn->setAttribute('endTime', $lastEnd);
        }
        //FIXME: add "endTime" att
        $domTurn = $domOut->createElement('Turn');
        $domTurn->setAttribute('startTime', $start);
        $domTurn->setAttribute('speaker', $speakers[$speaker]);
        $domTurn->setAttribute('mode', 'planned');
        $domTurn->setAttribute('channel', 'studio');
        $domSection->appendChild($domTurn);
    }

    $domSync = $domOut->createElement('Sync');
    $domSync->setAttribute('time', $start);
    $domTurn->appendChild($domSync);

    $domTxt = $domOut->createTextNode((string) $segment->text);
    $domTurn->appendChild($domTxt); 
    $lastEnd = $end;
}
if ($domTurn !== null) {
    $domTurn->setAttribute('endTime', $end);
}

echo $domOut->saveXML();
?>
