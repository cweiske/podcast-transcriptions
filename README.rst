*******************
Podcast-Transcripte
*******************

Ich möchte, daß meine Lieblingspodcasts Transkripte haben
- damit man sie auch morgen noch mit einer Textsuchmaschine wiederfindet.

Das ganze manuell zu machen ist sehr sehr zeitaufwändig, weshalb ich
das ganze so weit möglich automatisieren möchte.

Aktuell habe ich von einer Folge "Logbuch Netzpolitik" Sprechersegmentierungsdaten
von spokendata.com, und ein automatisches Transcript von Google Cloud Speech.

Jetzt gilt es herauszufinden, wie man diese Daten zusammenführt und dann
in ein Format wandelt, das man mit einem Transkript-Programm nachbearbeiten kann.

Danach muss HTML erstellt werden, aber das wird das kleinste Problem sein.

Linksammlung: http://p.cweiske.de/477


======
How-To
======

1. Mit Google Cloud Speech Transkription erstellen (``.json``)

   Script fehlt noch.

2. Mit spokendata.com Sprechersegmentierung (Diarzation) erstellen (``.xml``)

3. Transkript und Sprechersegmentierung zusammenführen::

     $ ./tools/merge-speech-diarization.php 1-google.json 1-spokendata.xml > 2-diarization-with-text.xml

4. Zusammengeführte Datei in ``.trs`` umwandeln für `Transcriber <http://trans.sourceforge.net/>`__::

     $ ./tools/convert-diarization.php 2-diarization-with-text.xml > 3-transcript.trs

5. In der Sprechersegmentierung werden viel zu viele unterschliedliche Sprecher erkannt.
   In transcriber die Sprecher benennen und auf die echten reduzieren - ``4-correctspeakers.trs``

6. In transcriber die Kapitelmarken einfügen

7. Von transcriber eingefügte Pausen und aufeinanderfolgende "Turns" zusammenfügen::

     $ ./tools/trs-compact.php 4-correctspeakers.trs > 5-compact-transcript.trs

8. Transkript in transcriber korrigieren.
   Dauerte bei mir 2:1, ~20 Minuten für 10 Minuten Podcast.

9. Fertiges Transcript in HTML umwandeln::

     $ xsltproc -o final-transcript.html tools/trs2html.xsl 6-final-transcript.trs


======
Status
======

Fertig
======
- LNP232 Der böse Kleber aus Deutschland

In Arbeit
=========


Todo
====
- LNP234 Mein Weg aus der Beschaffungskriminalität
- LNP233 Das Internet setzt sich endlich durch
- LNP231 Chronische Bitknappheit
- LNP230 Angst essen Seele auf
- LNP229 Deine Sandburg ist auf Sand gebaut
- LNP228 Interessierte Bürger
- LNP227 Magenta Terrorfrei
- LNP226 Merkelsche Rück
- LNP225 Dringende Bitte
- LNP224 Mit dem Abgriff in Frankfurt wird das alles in Ordnung kommen
