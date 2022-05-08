<?php

namespace PN\SeoBundle\Lib;

/**
 * SitemapGenerator
 *
 * This class used for generating Google Sitemap files
 * Cycle: Schema added for validation, time format changed to ISO
 */
class SitemapGenerator
{

    /**
     *
     * @var \XMLWriter
     */
    private $writer;

    private $path;

    private $fileName = 'sitemap';

    private $current_item = 0;

    private $current_sitemap = 0;

    const EXT = '.xml';

    const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    const DEFAULT_PRIORITY = 0.5;

    const ITEM_PER_SITEMAP = 50000;

    const SEPERATOR = '-';

    const INDEX_SUFFIX = 'index';


    /**
     * Returns \XMLWriter object instance
     *
     * @return \XMLWriter
     */
    private function getWriter()
    {
        return $this->writer;
    }

    /**
     * Assigns \XMLWriter object instance
     *
     * @param \XMLWriter $writer
     */
    private function setWriter(\XMLWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Returns the path of sitemaps
     *
     * @return string
     */
    private function getPath()
    {
        return $this->path;
    }

    /**
     * Sets paths of sitemaps
     *
     * @param string $path
     * @return SitemapGenerator
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Returns the fileName of the sitemap file
     *
     * @return string
     */
    private function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Sets fileName of sitemap file
     *
     * @param string $fileName
     * @return SitemapGenerator
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Returns current item count
     *
     * @return int
     */
    private function getCurrentItem()
    {
        return $this->current_item;
    }

    /**
     * Increases item counter
     */
    private function incCurrentItem()
    {
        $this->current_item = $this->current_item + 1;
    }

    /**
     * Returns current sitemap file count
     *
     * @return int
     */
    private function getCurrentSitemap()
    {
        return $this->current_sitemap;
    }

    /**
     * Increases sitemap file count
     */
    private function incCurrentSitemap()
    {
        $this->current_sitemap = $this->current_sitemap + 1;
    }

    /**
     * Prepares sitemap XML document
     */
    private function startSitemap()
    {
        $this->setWriter(new \XMLWriter());
        if ($this->getCurrentSitemap()) {
            $this->getWriter()->openURI($this->getPath().$this->getFileName().self::SEPERATOR.$this->getCurrentSitemap().self::EXT);
        } else {
            $this->getWriter()->openURI($this->getPath().$this->getFileName().self::EXT);
        }
        $this->getWriter()->startDocument('1.0', 'UTF-8');
        $this->getWriter()->setIndent(true);
        $this->getWriter()->startElement('urlset');
        $this->getWriter()->writeAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
        $this->getWriter()->writeAttribute('xsi:schemaLocation',
            "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd");
        $this->getWriter()->writeAttribute('xmlns', self::SCHEMA);
    }

    /**
     * Adds an item to the sitemap
     *
     * @param string $loc
     *            URL of the page. This value must be less than 2,048 characters.
     * @param string $priority
     *            The priority of this URL relative to other URLs on your SiteMapService. Valid values range from 0.0 to 1.0.
     * @param string $changefreq
     *            How frequently the page is likely to change. Valid values are always, hourly, daily, weekly, monthly, yearly and never.
     * @param string|int $lastmod
     *            The date of last modification of URL. Unix timestamp or any English textual DateTime description.
     * @return SitemapGenerator
     */
    public function addItem($loc, $priority = self::DEFAULT_PRIORITY, $changefreq = null, $lastmod = null)
    {
        if (($this->getCurrentItem() % self::ITEM_PER_SITEMAP) == 0) {
            if ($this->getWriter() instanceof \XMLWriter) {
                $this->endSitemap();
            }
            $this->startSitemap();
            $this->incCurrentSitemap();
        }
        $this->incCurrentItem();
        $this->getWriter()->startElement('url');
        if ($loc == "http://localhost/") {
            $this->getWriter()->writeElement('loc', $loc);
        } else {
            $this->getWriter()->writeElement('loc', $loc);
        }
        if ($lastmod) {
            $this->getWriter()->writeElement('lastmod', $this->getLastModifiedDate($lastmod));
        }
        if ($priority) {
            $this->getWriter()->writeElement('priority', $priority);
        }
        if ($changefreq) {
            $this->getWriter()->writeElement('changefreq', $changefreq);
        }
        $this->getWriter()->endElement();

        return $this;
    }

    /**
     * Prepares given a date for sitemap
     *
     * @param string $date
     *            Unix timestamp or any English textual datetime description
     * @return ISO8601 format.
     */
    private function getLastModifiedDate($date)
    {
        if (ctype_digit($date)) {
            return date('c', $date);
        } else {
            $date = strtotime($date);

            return date('c', $date);
        }
    }

    /**
     * Finalizes tags of sitemap XML document.
     */
    private function endSitemap()
    {
        if (!$this->getWriter()) {
            $this->startSitemap();
        }
        $this->getWriter()->endElement();
        $this->getWriter()->endDocument();
    }

    /**
     * Writes Google sitemap index for generated sitemap files
     *
     * @param string $loc
     *            Accessible URL path of sitemaps
     * @param string|int $lastmod
     *            The date of last modification of sitemap. Unix timestamp or any English textual datetime description.
     */
    public function createSitemapIndex($lastmod = 'Today')
    {
        $this->endSitemap();
        $indexwriter = new \XMLWriter();
        $indexwriter->openURI($this->getPath().$this->getFileName().self::SEPERATOR.self::INDEX_SUFFIX.self::EXT);
        $indexwriter->startDocument('1.0', 'UTF-8');
        $indexwriter->setIndent(true);
        $indexwriter->startElement('sitemapindex');
        $indexwriter->writeAttribute('xmlns', self::SCHEMA);
        for ($index = 0; $index < $this->getCurrentSitemap(); $index++) {
            $indexwriter->startElement('sitemap');
            $indexwriter->writeElement('loc', $this->getFileName().($index ? self::SEPERATOR.$index : '').self::EXT);
            $indexwriter->writeElement('lastmod', $this->getLastModifiedDate($lastmod));
            $indexwriter->endElement();
        }
        $indexwriter->endElement();
        $indexwriter->endDocument();
    }
}