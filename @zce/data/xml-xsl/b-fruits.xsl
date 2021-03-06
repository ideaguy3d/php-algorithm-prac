<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="no"/>
    <xsl:template match="fruits">
        <ul>
            <xsl:apply-templates/>
        </ul>
    </xsl:template>
    <xsl:template match="fruit">
        <li><xsl:value-of select="fruit"/></li>
    </xsl:template>
</xsl:stylesheet>