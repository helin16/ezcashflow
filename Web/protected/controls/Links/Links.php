<?php
/**
 * The Usefull Links Module
 *
 * @package    Web
 * @subpackage Controls
 * @author     lhe
 *
 */
class Links extends TPanel
{
    /**
     * (non-PHPdoc)
     * @see TPanel::renderEndTag()
     */
    public function renderEndTag($writer)
    {
        $html = '<ul>';
            $html .= '<li>';
                $html .= '<a href="http://www.anz.com.au/" target="__blank">';
                    $html .= '<p class="link">Anz</p>';
                    $html .= '<p class="descr">134152326</p>';
                $html .= '</a>';
            $html .= '</li>';
            $html .= '<li>';
                $html .= '<a href="http://www.nab.com.au/" target="__blank">';
                    $html .= '<p class="link">Nab</p>';
                    $html .= '<p class="descr">13455047</p>';
                $html .= '</a>';
            $html .= '</li>';
            $html .= '<li>';
                $html .= '<a href="http://www.westpac.com.au/" target="__blank">';
                    $html .= '<p class="link">Westpac</p>';
                    $html .= '<p class="descr">92247676</p>';
                $html .= '</a>';
            $html .= '</li>';
        $html .= '</ul>';
        $writer->write($html);
        parent::renderEndTag($writer);
    }
}