<?php
return array(
    //md文件存储的路径
    'save_path' => BASE_PATH . 'posts',

    'blog_base_url' => 'http://blog.csdn.net',

    //需要转换的文章的url，下面的archive_url未设置时，该设置有效
    'post_url' => 'http://blog.csdn.net/sky_zhe/article/details/9702489',

    //文章列表，当设置了这个，上面的post_url设置无效
    //'archive_url' => 'http://blog.csdn.net/sky_zhe',
    'archive_url' => '',
    //是否在文章头部显示日志属性，包括title,date,tag等
    'inject_md_header' => true,

    'github_code_block_style' => true
);