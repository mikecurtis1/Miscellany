<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>
<xsl:template match="/">
  <html>
    <head>
      <title>Aleph OPAC Item</title>
      <link rel="stylesheet" type="text/css" href="item.css" media="screen" />
    </head>
    <body>
    <div class="main">
    <h1><xsl:value-of select="//varfield[@id='245']"/></h1>
    <table>
      <xsl:for-each select="//record/metadata/oai_marc/*">
        <xsl:choose>
          <xsl:when test="name(.)='fixfield'">
            <tr>
              <td class="fieldtype">
                <xsl:value-of select="name(.)"/>
              </td>
              <td class="marc_tag">
                <xsl:value-of select="@id"/>
              </td>
              <td class="data">
                <xsl:value-of select="."/>
              </td>
            </tr>
          </xsl:when>
          <xsl:when test="name(.)='varfield'">
            <xsl:for-each select="subfield">
              <tr>
                <td>
                  <xsl:value-of select="name(..)"/>
                </td>
                <td>
                  <xsl:value-of select="../@id"/>
                  <xsl:value-of select="@label"/>
                </td>
                <td>
                  <xsl:choose>
                    <xsl:when test="../@id='856' and @label='u'">
                      <a>
                        <xsl:attribute name="href"><xsl:value-of select="."/>
                        </xsl:attribute>
                        <xsl:value-of select="."/>
                      </a>
                    </xsl:when>
                    <xsl:when test="../@id='650' and @label='a'">
                      <a>
                        <xsl:attribute name="href">http://ups.sunyconnect.suny.edu:4360/F?func=find-c&amp;ccl_term=wsu%3D<xsl:value-of select="."/>
                        </xsl:attribute>
                        <xsl:value-of select="."/>
                      </a>
                    </xsl:when>
                    <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
                  </xsl:choose>
                </td>
              </tr>
            </xsl:for-each>
          </xsl:when>
          <xsl:otherwise></xsl:otherwise>
        </xsl:choose>
      </xsl:for-each>
    </table>
    <div class="opac_view">
      <a>
        <xsl:attribute name="href">
          http://ups.sunyconnect.suny.edu:4360/F?func=direct&amp;doc_number=
          <xsl:value-of select="format-number(//fixfield[@id='001'],'000000000')"/>
        </xsl:attribute>
        <xsl:text>OPAC View</xsl:text>
      </a>
    </div>
    </div>
    </body>
  </html>
</xsl:template>
</xsl:stylesheet>