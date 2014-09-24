<?php
/**
 *这是一个将传统博客系统迁移到使用Markdown格式书写的静态博客系统的工具。
 *它能识别并截取HTML页面的文章内容，再转换成Markdown格式。注意目前仅支持 CSDN Blog!
 *
 * @author baizhe <baizhebz@gmail.com>
 */

/**
 * 如果你的PHP没有安装cURL扩展，请先安装
 */
if ( ! function_exists('curl_init')) {
    die('The tool needs the cURL PHP extension.');
}

define('VERSION', '1.0');

define('BASE_PATH', __DIR__ . '/');

require 'common.php';

require BASE_PATH . 'lib/ArchivePageParser.php';

require BASE_PATH . 'lib/PostPageParser.php';

if (config_item('post_url') == '' && config_item('archive_url') == '') {
    die('request url does not setting correct');
}

$post_list = array();

if ($archive_url = config_item('archive_url')) {

    $archive_parser = new ArchivePageParser();

    do {
        //加上'?viewmode=contents'的后缀，一页能显示更多条内容
        $archive_parser->init($archive_url . '?viewmode=contents');

        $archive_parser->parse();

        $post_list = array_merge($archive_parser->get_post_list(), $post_list);

        if ($archive_parser->has_next_page()) {
            $archive_url = $archive_parser->next_page_url();
        }

        $archive_parser->free();
    } while ($archive_parser->has_next_page());

} else {
    $post_list[] = array(
        'url' => config_item('post_url')
    );
}

$post_parser = new PostPageParser();
foreach ($post_list as $index=>$each_post) {
    $post_parser->init($each_post['url']);
    $post_parser->parse();
    $post_parser->save2md();

    $title = isset($each_post['title']) ? $each_post['title'] : $post_parser->get_title();
    echo 'the ' . ordinalize(++$index). ' article already generated, title: ' . $title;
    echo "\n";

    @ob_flush();
    flush();
}

