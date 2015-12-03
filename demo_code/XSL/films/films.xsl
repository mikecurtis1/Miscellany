<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

  <xsl:template match="/">
   <html>
   <head>
     <meta name="viewport" content="width=device-width, initial-scale=1" />
     <link rel="stylesheet" type="text/css" href="films.css" media="screen" />
   <title>Film Collection :: XML</title>
   </head>
    <xsl:apply-templates/>
   </html>
  </xsl:template>

  <xsl:template match="film_collection">
   <body>
   <hr />
    <xsl:apply-templates/>
   </body>
  </xsl:template>

  <xsl:template match="film">
   <div class="film">
    <xsl:apply-templates/>
   </div>
   <hr />
  </xsl:template>

  <xsl:template match="title">
   <div class="title">
    <xsl:apply-templates/>
   <div class="date_entered">Date entered : <xsl:value-of select="//@date_entered"/></div>
   </div>
  </xsl:template>

  <xsl:template match="title/original_title">
   <div class="original_title">
   <a>
    <xsl:if test="../../@amg">
     <xsl:attribute name="href">
      <xsl:value-of select="//@amgbase"/><xsl:text>movie/</xsl:text><xsl:value-of select="../../@amg"/>
     </xsl:attribute>
    </xsl:if>
    <xsl:value-of select="."/>
   </a> (<xsl:value-of select="@language"/>)
   </div>
  </xsl:template>

  <xsl:template match="title/other_title">
   <div class="other_title">
    <xsl:apply-templates/> (<xsl:value-of select="@language"/>)
   </div>
  </xsl:template>

  <xsl:template match="awards">
   <div class="section">
   <div class="heading">Awards: </div>
    <xsl:apply-templates/>
   </div>
  </xsl:template>

  <xsl:template match="award">
    <div class="indented">
     <xsl:apply-templates/>
    </div>
  </xsl:template>

  <xsl:template match="locations">
   <div class="section">
   <div class="heading">Locations: </div>
    <xsl:apply-templates/>
   </div>
  </xsl:template>

  <xsl:template match="locations/filmed_in|locations/set_in">
    <div class="indented">
     <xsl:value-of select="translate(name(.), '_', ' ')"/> : <xsl:apply-templates/>
    </div>
  </xsl:template>

  <xsl:template match="date">
   <div class="section">
   <div class="heading">Dates: </div>
    <xsl:apply-templates/>
   </div>
  </xsl:template>

  <xsl:template match="date/release_date|date/setting_date">
    <div class="indented">
     <xsl:value-of select="translate(name(.), '_', ' ')"/> : <xsl:apply-templates/>
    </div>
  </xsl:template>

  <xsl:template match="personnel">
   <div class="section">
     <div class="heading">Personnel: </div>
   <ul>
    <xsl:apply-templates/>
   </ul>
     </div>
  </xsl:template>

  <xsl:template match="director|cinematographer|composer|screenplay">
    <li><xsl:value-of select="name(.)"/> : 
    <a>
     <xsl:if test="@amg">
      <xsl:attribute name="href">
       <xsl:value-of select="//@amgbase"/><xsl:text>artist/</xsl:text><xsl:value-of select="@amg"/>
      </xsl:attribute>
     </xsl:if>
     <xsl:value-of select="."/>
    </a>
     <xsl:if test="@award"> (</xsl:if>
      <xsl:value-of select="@award"/>
     <xsl:if test="@award">)</xsl:if>
    </li>
  </xsl:template>

  <xsl:template match="cast">
   <div class="section">
     <div class="heading">Cast: </div>
   <ul>
    <xsl:for-each select="lead|supporting">
     <li><xsl:value-of select="name(.)"/> : 
     <a>
      <xsl:if test="@amg">
       <xsl:attribute name="href">
        <xsl:value-of select="//@amgbase"/><xsl:text>artist/</xsl:text><xsl:value-of select="@amg"/>
       </xsl:attribute>
      </xsl:if>
      <xsl:value-of select="."/>
     </a> playing <xsl:value-of select="@character"/>
      <xsl:if test="@award"> (</xsl:if>
       <xsl:value-of select="@award"/>
      <xsl:if test="@award">)</xsl:if>
     </li>
    </xsl:for-each>
   </ul>
     </div>
  </xsl:template>

  <xsl:template match="languages">
   <div class="section">
   <div class="heading">Languages: </div>
    <xsl:apply-templates/>
   </div>
  </xsl:template>
    
    <xsl:template match="languages/original_language|languages/translation">
    <div class="indented">
     <xsl:value-of select="translate(name(.), '_', ' ')"/> : <xsl:apply-templates/>
    </div>
  </xsl:template>

</xsl:stylesheet>