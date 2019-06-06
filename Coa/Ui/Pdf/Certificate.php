<?php
namespace Coa\Ui\Pdf;

use Dom\Renderer\Renderer;
use Dom\Template;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 *
 * @note This file uses the mpdf lib
 * @link https://mpdf.github.io/
 */
class Certificate extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{

    /**
     * @var \Coa\Adapter\Iface
     */
    protected $adapter = null;

    /**
     * @var \Mpdf\Mpdf
     */
    protected $mpdf = null;

    /**
     * @var string
     */
    protected $watermark = '';

    /**
     * @var bool
     */
    private $rendered = false;


    /**
     * HtmlInvoice constructor.
     * @param \Coa\Adapter\Iface $adapter
     * @param string $watermark
     * @throws \Exception
     */
    public function __construct($adapter, $watermark = '')
    {
        $this->adapter = $adapter;
        $this->watermark = $watermark;

        $this->initPdf();
    }

    /**
     * @param \Coa\Adapter\Iface $adapter
     * @param string $watermark
     * @return Certificate
     * @throws \Exception
     */
    public static function create($adapter, $watermark = '')
    {
        $obj = new self($adapter, $watermark);
        return $obj;
    }

    /**
     * @return \Coa\Adapter\Iface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return \Coa\Db\Coa
     */
    public function getCoa()
    {
        return $this->getAdapter()->getCoa();
    }

    /**
     * @throws \Exception
     */
    protected function initPdf()
    {
        $html = $this->show()->toString();

        $tpl = \Tk\CurlyTemplate::create($html);
        $parsedHtml = $tpl->parse($this->adapter->all());

        $this->mpdf = new \Mpdf\Mpdf(array(
			'format' => 'A4-L',
            'orientation' => 'L',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir' => $this->getConfig()->getTempPath()
        ));
        $mpdf = $this->mpdf;
        //$mpdf->setBasePath($url);

        //$mpdf->shrink_tables_to_fit = 0;
        //$mpdf->useSubstitutions = true; // optional - just as an example
        //$mpdf->SetHeader($url . "\n\n" . 'Page {PAGENO}');  // optional - just as an example
        //$mpdf->CSSselectMedia='mpdf'; // assuming you used this in the document header
        //$mpdf->SetProtection(array('print'));

        $mpdf->SetTitle('Certificate');
        $mpdf->SetAuthor($this->getCoa()->getProfile()->getInstitution()->getName());

        if ($this->watermark) {
            $mpdf->SetWatermarkText($this->watermark);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
        }
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($parsedHtml);
    }

    /**
     * Output the pdf to the browser
     *
     * @throws \Mpdf\MpdfException
     */
    public function output()
    {
        $filename = 'Coa-' . $this->getCoa()->getId() . '-'.$this->getAdapter()->getModel()->getId().'.pdf';
        $this->mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
    }

    /**
     * Retun the PDF as a string to attache to an email message
     *
     * @param string $filename
     * @return string
     * @throws \Mpdf\MpdfException
     */
    public function getPdfAttachment($filename = '')
    {
        if (!$filename)
            $filename = 'Coa-' . $this->getCoa()->getId() . '-'.$this->getAdapter()->getModel()->getId().'.pdf';
        return $this->mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN);
    }

    /**
     * Execute the renderer.
     * Return an object that your framework can interpret and display.
     *
     * @return null|Template|Renderer
     * @throws \Exception
     */
    public function show()
    {
        $template = $this->getTemplate();
        if ($this->rendered) return $template;
        $this->rendered = true;

        $template->appendHtml('content', $this->getCoa()->html);

        if ($this->getCoa()->getBackgroundUrl()) {
            $template->setAttr('body', 'style', 'background-image: url('.$this->getCoa()->getBackgroundUrl().');background-image-resize: 4; background-image-resolution: from-image;');
        }

        return $template;
    }

    /**
     * DomTemplate magic method
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title></title>
</head>
<body class="" style="" var="body">
  <div var="content"></div>
</body>
</html>
HTML;

        return \Dom\Loader::load($xhtml);
    }

    /**
     * @return \App\Config|\Tk\Config
     */
    public function getConfig()
    {
        return \App\Config::getInstance();
    }
}