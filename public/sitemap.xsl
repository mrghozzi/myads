<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                exclude-result-prefixes="sitemap">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <html lang="en">
            <head>
                <title>XML Sitemap</title>
                <style type="text/css">
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; color: #374151; margin: 0; padding: 2rem; background: #f9fafb; line-height: 1.5; }
                    .wrapper { max-width: 1000px; margin: 0 auto; background: #ffffff; padding: 2.5rem; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; }
                    h1 { font-size: 1.875rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 1rem; }
                    .info { background: #eff6ff; color: #1e40af; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; font-size: 0.875rem; }
                    table { width: 100%; border-collapse: collapse; margin-top: 1rem; text-align: left; }
                    th { padding: 0.75rem 1rem; border-bottom: 2px solid #f3f4f6; color: #6b7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
                    td { padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem; color: #374151; word-break: break-all; }
                    tr:hover td { background: #f9fafb; }
                    .footer { margin-top: 3rem; font-size: 0.75rem; color: #9ca3af; text-align: center; }
                    a { color: #2563eb; text-decoration: none; border-bottom: 1px solid transparent; }
                    a:hover { border-bottom-color: #2563eb; }
                    .badge { display: inline-block; padding: 0.125rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; background: #dbeafe; color: #1e40af; margin-left: 0.5rem; }
                </style>
            </head>
            <body>
                <div class="wrapper">
                    <xsl:choose>
                        <xsl:when test="sitemap:sitemapindex">
                            <h1>Sitemap Index</h1>
                            <div class="info">
                                This index contains <span class="badge"><xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/></span> individual sitemaps.
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Sitemap</th>
                                        <th>Last Modified</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                                        <tr>
                                            <td>
                                                <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                                            </td>
                                            <td><xsl:value-of select="sitemap:lastmod"/></td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </xsl:when>
                        <xsl:otherwise>
                            <h1>XML Sitemap</h1>
                            <div class="info">
                                This sitemap contains <span class="badge"><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></span> URLs.
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>URL</th>
                                        <th>Priority</th>
                                        <th>Freq</th>
                                        <th>Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="sitemap:urlset/sitemap:url">
                                        <tr>
                                            <td>
                                                <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                                            </td>
                                            <td><xsl:value-of select="sitemap:priority"/></td>
                                            <td><xsl:value-of select="sitemap:changefreq"/></td>
                                            <td><xsl:value-of select="sitemap:lastmod"/></td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </xsl:otherwise>
                    </xsl:choose>
                    <div class="footer">
                        Powered by MyAds SEO Engine | v4.2.0
                    </div>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
