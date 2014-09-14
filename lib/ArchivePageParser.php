<?php
class ArchivePageParser {

    //the blog achieve url
    private $_request_url;

    private $_post_list;

    //DomDocument Instance
    private $_doc;

    private $_has_next_page;

    private $_next_page_url;

    //Response content.
    private $_source;

    public function __construct() {}

    public function init($request_url) {

        $this->_request_url = $request_url;

        $this->_has_next_page = false;

        $this->_next_page_url = '';

        $this->_post_list = array();

        $this->_source = make_request($this->_request_url);
    }

    public function parse() {

        $this->_doc = new DOMDocument('1.0', 'utf-8');
        @$this->_doc->loadHTML('<?xml encoding="utf-8">'.$this->_source);

        $blog_base_url = config_item('blog_base_url');

        $all_divs = $this->_doc->getElementsByTagName('div');
        $i = 0;
        while ($div = $all_divs->item($i++)) {
            $div_class = $div->hasAttribute('class') ? $div->getAttribute('class') : '';
            if (strpos($div_class, 'list_item') === 0) {
                $_anchors = $div->getElementsByTagName('a');
                $j = 0;
                while ($target = $_anchors->item($j++)) {
                    if ($target->parentNode->hasAttribute('class') && $target->parentNode->getAttribute('class') === 'link_title') {
                        $this->_post_list[] = array(
                            'url' => $blog_base_url . $target->getAttribute('href'),
                            'title' => trim($target->nodeValue)
                        );
                    }
                }
            } else if ($div_class === 'pagelist') {
                $pagination_div = $div;
            }
        }

        if (isset($pagination_div)) {
            $_page_anchors = $pagination_div->getElementsByTagName('a');
            $x = 0;
            while ($_anchors = $_page_anchors->item($x++)) {
                if (trim($_anchors->nodeValue) == '下一页') {
                    $this->_has_next_page = true;
                    $this->_next_page_url = $blog_base_url . $_anchors->getAttribute('href');
                }
            }
        }
    }

    public function get_post_list() {
        return $this->_post_list;
    }

    public function has_next_page() {
        return $this->_has_next_page;
    }

    public function next_page_url() {
        return $this->_next_page_url;
    }

    public function free() {
        unset($this->_doc);
        $this->_doc = null;
        unset($this->_source);
        $this->_source = '';
    }
}