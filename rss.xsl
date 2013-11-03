<?xml version="1.0" encoding="utf-8"?><!DOCTYPE xsl:stylesheet [ <!ENTITY nbsp "&#160;"><!ENTITY copy "&#169;"><!ENTITY reg "&#174;"><!ENTITY trade "&#8482;"><!ENTITY mdash "&#8212;"><!ENTITY ldquo "&#8220;"><!ENTITY rdquo "&#8221;"> <!ENTITY pound "&#163;"><!ENTITY yen "&#165;"><!ENTITY euro "&#8364;">]>
<!-- Aragorn.cz RSS StyleSheet :) -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!-- Global Definitions -->
<xsl:output method="html" omit-xml-declaration="yes" encoding="utf-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>
<xsl:strip-space elements="*" />
<!-- Main Template -->
<xsl:template match="/rss">
<html>
<head>
	<title><xsl:value-of select="channel/title" /></title>
    <link type="text/css" rel="stylesheet" href="/css/gallery.css" />
	<script type="text/javascript" src="/js/mootools.js"></script>
</head>
<body>
<div class='holder'>
	<div class="top">
		<h1><a href="/"><span><xsl:value-of select="channel/title"/></span></a></h1>
	</div>
	<div class='content' style="float:none !important; text-align:center; margin: 0 auto !important">
		<div>
			<div class='frame' style="text-align:left;">
				<div class='topframe'></div>
				<div class='frame-in'>
					<h2 class='h2-head'>
						<xsl:element name="a">
								<xsl:attribute name="href"><xsl:value-of select="channel/link" /></xsl:attribute>
								<xsl:attribute name="title"><xsl:value-of select="channel/description" /></xsl:attribute>
								<xsl:value-of select="channel/title" />
						</xsl:element>
 					</h2>
					<h3>&nbsp;</h3>
					<p class="text t-a-c" style="margin-bottom:20px;"><xsl:value-of select="channel/description" /></p>
					<xsl:for-each select="channel/item">
						<div class='highlight-top'></div>
						<div class='highlight-mid'>
							<div class="art" style="padding:0 !important;margin: 0 !important;"><span style="padding:0 5px"></span><xsl:element name="a">
								<xsl:attribute name="href"><xsl:value-of select="link" /></xsl:attribute>
								<xsl:attribute name="title"><xsl:value-of select="title" /> :: <xsl:value-of select="link" /></xsl:attribute>
								<xsl:attribute name="class">permalink</xsl:attribute>
								<xsl:value-of select="title" />
							</xsl:element>
							<div class="text">
								<xsl:value-of select="description" />
							</div>
							</div>
						</div>
						<div class="highlight-bot"></div>
					</xsl:for-each>
				</div>
				<div class="bottomframe"></div>
			</div>
		</div>
	</div>
	<div class="footer"><div class="footer2">&copy;&nbsp;Aragorn.cz <span><xsl:value-of select="channel/year" /></span></div>
	</div>
</div>
<script charset="utf-8" type="text/javascript">/* <![CDATA[ */window.addEvent('domready',function(){
$$(".text").each(function (p) {
	p.set('html', p.get('text'));
});
});/* ]]> */</script>
</body>
</html>
</xsl:template>

</xsl:stylesheet>