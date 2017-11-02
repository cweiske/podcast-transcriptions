#!/usr/bin/env php
<?php
/**
 * Merge google speech json with spokendata diarization XML file (without text).
 * Generates a diarization XML file with text.
 */
if ($argc < 3) {
    echo "Parameters missing: google-speech.json spokendata-diarization.xml [offset]\n";
    exit(1);
}
$fileGoogleSpeech = $argv[1];
$fileDiarization = $argv[2];

$offset = 0;
if ($argc >= 4) {
    $offset = floatval($argv[3]);
}

if (!file_exists($fileGoogleSpeech)) {
    echo "Google speech file does not exist\n";
    exit(1);
}
if (!file_exists($fileDiarization)) {
    echo "Diarization file does not exist\n";
    exit(1);
}

$data = json_decode(file_get_contents($fileGoogleSpeech));
$xml  = simplexml_load_file($fileDiarization);

function gsIterate($data) {
    foreach ($data->response->results as $result) {
        foreach ($result->alternatives as $alternative) {
            foreach ($alternative->words as $word) {
                yield $word;
            }
        }
    }
}

$result = [];

$time = 0.0;//seconds
$wordGen = gsIterate($data);
foreach ($xml->segment as $segment) {
    $segStart   = (float) $segment->start;
    $segEnd     = (float) $segment->end;
    $segSpeaker = (string) $segment->speaker;
    //echo "Segment: $segStart - $segEnd\n";

    $text = '';
    while ($word = $wordGen->current()) {
        //$wordStart = floatval(rtrim($word->startTime, 's'));
        //if ($wordStart + $offset > $segEnd) {
        $wordEnd = floatval(rtrim($word->endTime, 's'));
        if ($wordEnd + $offset > $segEnd) {
            break;
        }
        //echo "Word: $wordStart\n";
        $text .= ' ' . $word->word;
        $wordGen->next();
    };

    $text = trim($text);
    //echo $segSpeaker . ': ' . $text . "\n";
    $segment->text = $text;
}

echo $xml->asXML();
?>
