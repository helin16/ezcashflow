<?php
/**
 * The admin page
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class AdminController extends PageAbstract  
{
    /**
     * (non-PHPdoc)
     * @see PageAbstract::onLoad()
     */
    public function onLoad($param)
    {
        parent::onLoad($param);
        if(!$this->IsPostBack)
        {
            $this->_addRightPanel($this->_loadRightPanel());
        }
    }
    /**
     * Load right panel
     *
     * @return string The HTML code in that panel
     */
    private function _loadRightPanel()
    {
        $html = '<div class="box">';
            $html .= '<div class="title">Menu</div>';
            $html .= '<div class="content">';
                $html .= $this->_getLinks();
            $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    /**
     * Getting the links
     *
     * @return string
     */
    private function _getLinks()
    {
        $html = '<ul class="menulinks">';
            $html .= '<li>';
                $html .= '<a href="/admin/assettype" onclick="return pageJs.changePage(this);">';
                    $html .= '<p class="link">Asset Type</p>';
                    $html .= '<p class="descr">Asset Type Manager</p>';
                $html .= '</a>';
            $html .= '</li>';
            $html .= '<li>';
                $html .= '<a href="/admin/backup" onclick="return pageJs.changePage(this);">';
                    $html .= '<p class="link">Backup/Restore</p>';
                    $html .= '<p class="descr">Backup or Restore database</p>';
                $html .= '</a>';
            $html .= '</li>';
        $html .= '</ul>';
        return $html;
    }
}
?>