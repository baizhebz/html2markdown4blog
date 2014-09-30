<?php
class PostPageParser {

    //the blog achieve url
    private $_request_url;

    private $_title;

    // A UNIX Timestamp
    private $_date;

    private $_tags;

    private $_categories;

    private $_md_content;

    //Response content.
    private $_source;

    public function __construct() {}

    public function init($request_url) {

        $this->reset();

        $this->_request_url = $request_url;

        $this->_source = make_request($this->_request_url);

        $this->_prepare4csdn();
    }

    public function reset() {
        $this->_title = '';
        $this->_date = '';
        $this->_tags = array();
        $this->_categories = array();
        $this->_md_content = '';
        unset($this->_source);
        $this->_source = '';
    }

    private function _prepare4csdn() {
        //去掉空的段落 <p></p>
        $this->_source = preg_replace("#<p></p>#", "<br>", $this->_source);
        //csdn里，你在编辑器里敲一个换行，可能会被转换成<p><br></p>的形式
        $this->_source = preg_replace("#<p>\s*(<br/?>)+\s*</p>#i", "<br>", $this->_source);
        //csdn里，<p>标签结束前会加上一个<br/>
        $this->_source = preg_replace("#<br/?>\s*</p>#i", "</p>", $this->_source);
    }

    public function parse() {

        $doc = new DOMDocument('1.0', 'utf-8');
        @$doc->loadHTML('<?xml encoding="utf-8">'.$this->_source);

        $content_node = null;

        $all_divs = $doc->getElementsByTagName('div');
        $i = 0;
        while($div = $all_divs->item($i++)) {
            $div_class = $div->getAttribute("class");
            switch ($div_class) {
                case 'article_title':
                    $title = trim($div->nodeValue);
                    $title = str_replace(array('\n'), '', $title);
                    $this->_title = $title;
                    break;

                case 'article_manage':
                    foreach ($div->childNodes as $_mag_item) {
                        if ($_mag_item->nodeType == XML_ELEMENT_NODE) {
                            $_class = $_mag_item->getAttribute('class');
                            if ($_class == 'link_categories') {
                                $_anchors = $_mag_item->getElementsByTagName('a');
                                $_i = 0;
                                while ($_a = $_anchors->item($_i++)) {
                                    $this->_categories[] = trim($_a->nodeValue);
                                }
                            } else if ($_class == 'link_postdate') {
                                $_date = trim($_mag_item->nodeValue);
                                $this->_date = strtotime($_date);
                            }
                        }
                    }
                    break;

                case 'tag2box':
                    foreach ($div->childNodes as $_tag_item) {
                        $_tag = trim($_tag_item->nodeValue);
                        if ($_tag != '' && $_tag !== 'null') {
                            $this->_tags[] = $_tag;
                        }
                    }
                    break;

                case 'article_content':
                    $content_node = $div;
                    break;

                default:
                    # code...
                    break;
            }
        }
        unset($doc);

        $this->_md_content = @$this->_parse_element($content_node);
    }

    public function save2md() {

        $front_matter_style = config_item('front-matter');
        if ($front_matter_style !== 'none' && $front_matter_style != '') {
            $this->_md_content = $this->_make_front_matter($front_matter_style) . $this->_md_content;
        }

        //去掉文件名中不可用的符号
        $file_name = str_replace(array('\\', '/', '?', ':', '*', '<', '>', '"', '|'), '', $this->_title) . '.' .'md';

        //windows下中文文件名乱码，其他系统不清楚会不会有这个问题
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $file_name = @iconv("utf-8", "gb2312//IGNORE", $file_name);
        }
        if (config_item('date_ahead_filename')) {
            $file_name = date('Y-m-d-', $this->_date).$file_name;
        }

        file_put_contents(config_item('save_path').'/'.$file_name, $this->_md_content);
    }

    private function _make_front_matter($style) {
        $style = strtolower($style);
        $prepared_styles = require BASE_PATH . 'front-matter.php';
        if ( ! isset($prepared_styles[$style])) {
            return '';
        }

        $front_matter = $prepared_styles[$style];

        if (isset($prepared_styles['date_format'])) {
            $date = date($prepared_styles['date_format'], $this->_date);
        } else {
            $date = date('Y-m-d H:i:s', $this->_date);
        }


        $front_matter = preg_replace('#\{\stitle\s\}#', $this->_title, $front_matter);
        $front_matter = preg_replace('#\{\sdate\s\}#', $date, $front_matter);
        $glue = ($style === strtolower('jekyll')) ? ", " : "\n- ";
        $front_matter = preg_replace('#\{\scategories\s\}#', join($glue, $this->_categories), $front_matter);
        $front_matter = preg_replace('#\{\stags\s\}#', join($glue, $this->_tags), $front_matter);

        return $front_matter;
    }

    /**
     * 解析html各元素，拼成markdown格式的内容
     * inspired by TheFox-html2markdown: https://github.com/TheFox/html2markdown
     * @param $node
     * @return string
     */
    private function _parse_element($node) {
        $markdown = '';
        $text_pre = '';
        $text_pre_allLines = '';
        $text = '';
        $text_post = '';
        static $ol_no = 0;

        $tag_name = $node->nodeName;
        $apply_general_code_style = false;

        if ($node->nodeType == XML_TEXT_NODE) {
            $text = $node->wholeText;

            if ($node->parentNode->nodeName != 'code' && $node->parentNode->nodeName != 'pre') {
                $text = preg_replace('#^\s*$#', '', $text);
                if ($node->parentNode->nodeName == 'p') {
                    $text = trim($text);
                } else {
                    $text = trim($text, "\n");
                    $text = trim($text, "\r\n");
                }
            }
        }
        else if ($node->nodeType == XML_ELEMENT_NODE) {
            if ($tag_name == 'p') {
                if ($node->parentNode->nodeName == 'blockquote') {
                    $text_pre = '> ';
                    $text_pre_allLines = '> ';
                }
                $text_post = "\n";
            }
            else if ($tag_name == 'i' || $tag_name == 'em') {
                $text_pre = '*';
                $text_post = '*';
            }
            else if ($tag_name == 'b' || $tag_name == 'strong') {
                $text_pre = '**';
                $text_post = '**';
            }
            else if ($tag_name == 'a') {
                $text_pre = '[';
                $text_post = ']('.$node->getAttribute('href');
                $text_post .= ($node->hasAttribute('title') ? ' "'.$node->getAttribute('title').'"' : '').')';
            }
            else if ($tag_name == 'img') {
                $text_pre = '![';
                $text = $node->hasAttribute('alt') ? $node->getAttribute('alt') : '';
                $text_post = ']('.$node->getAttribute('src');
                $text_post .= ($node->hasAttribute('title') ? ' "'.$node->getAttribute('title').'"' : '').')';
            }
            else if ($tag_name == 'pre') {
                //for csdn
                $is_code = $node->hasAttribute('name') ? ($node->getAttribute('name') === 'code' ? true :false) : false;
                if ($is_code) {
                    if (config_item('github_code_block_style') == true) {
                        $lang = $node->hasAttribute('class') ? $node->getAttribute('class') : '';
                        $text_pre = "```$lang\n";
                        $text_post = "\n```\n";
                    } else {
                        $apply_general_code_style = true;
                    }
                } else if($node->firstChild->nodeName != 'code') {
                    $apply_general_code_style = true;
                }
            }
            else if ($tag_name == 'code'){
                if ($node->parentNode->nodeName == 'pre') {
                    $apply_general_code_style = true;
                } else {
                    $text_pre = '`';
                    $text_post = '`';
                }
            }
            else if ($tag_name == 'br') {
                $text_post = "\n";
            }
            else if ($tag_name == 'ul') {
                $text_post = "\n";
            }
            else if ($tag_name == 'ol') {
                $text_post = "\n";
                $ol_no = 1;
            }
            else if ($tag_name == 'li') {
                if ($node->parentNode->nodeName == 'ul') {
                    $text_pre = '- ';
                }
                else if ($node->parentNode->nodeName == 'ol') {
                    //TODO: 支持ol嵌套
                    $text_pre = $ol_no++.'. ';
                }
                $text_post = "\n";
            }
            else if (in_array($tag_name, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))) {
                $level = (int)strrev($tag_name);
                $text_pre = str_repeat('#', $level);
                $text_post = "\n";
            }
            else if ($tag_name == 'hr') {
                $text_pre = '***';
                $text_post = "\n";
            }
            else if ($tag_name == 'del') {
                $text_pre = '~~';
                $text_post = '~~';
            }
            else {
                //do nothing
            }
        }

        if ($apply_general_code_style) {
            $text_pre = "\t";
            $text_pre_allLines = "\t";
            $text_post = "\n";
        }

        if ($node->hasChildNodes()) {
            foreach($node->childNodes as $node){
                $text .= $this->_parse_element($node);
            }
        }

        if ($text_pre_allLines) {
            $text = str_replace("\n", "\n".$text_pre_allLines, $text);
        }

        $markdown .= $text_pre.$text.$text_post;
        return $markdown;
    }

    public function get_title() {
        return $this->_title;
    }
}
