<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns="http://www.w3.org/1999/xhtml">
 <xsl:output mode="xml" encoding="UTF-8" indent="yes"/>


 <xsl:template match="/Trans">
<html>
 <head>
  <meta charset="utf-8"/>
  <title>Transcript of <xsl:value-of select="@audio_filename"/></title>
  <meta name="generator" content="trs2html"/>
  <style type="text/css">
.transcript .turn {
    padding-left: 11em;
}
.transcript .speaker {
    position: absolute;
    left: 1ex;
    margin-top: 0px;
}
.transcript a.play {
    text-decoration: none;
    opacity: 0.3;
}
.transcript .turn p:hover a.play {
    opacity: 1;
}
  </style>
 </head>
 <body>
  <section class="transcript">
   <xsl:for-each select="Episode">
    <xsl:for-each select="Section">
     <xsl:apply-templates select="."/>
    </xsl:for-each>
   </xsl:for-each>
  </section>
 </body>
</html>
 </xsl:template>


 <xsl:template match="Section">
  <h2>
   <xsl:value-of select="/Trans/Topics/Topic[@id=current()/@topic]/@desc"/>
  </h2>

  <xsl:for-each select="Turn">
   <div class="turn">
    <p class="speaker">
     <xsl:value-of select="/Trans/Speakers/Speaker[@id=current()/@speaker]/@name"/>
    </p>

    <xsl:for-each select="Sync">
     <xsl:variable name="seconds">
      <xsl:number value="@time"/>
     </xsl:variable>
     <p>
      <a href="#t={$seconds}" class="play">&#9654;</a>
      <xsl:value-of select="following-sibling::text()"/>
     </p>
    </xsl:for-each>
   </div>
  </xsl:for-each>
 </xsl:template>


</xsl:stylesheet>

