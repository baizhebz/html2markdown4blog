<?php
return array(
    //md文件存储的路径
    'save_path' => BASE_PATH . 'posts',

    'blog_base_url' => 'http://blog.csdn.net',

    //目录列表页的url，如果设置了这一项，则下面的post_url设置无效
    'archive_url' => '',

    //某一篇文章的url，上面的archive_url未设置时，该设置有效
    'post_url' => '',

    //文件头部加入哪种YAML front-matter，可选的有：Jekyll(Octopress),Hexo
    //也可以填none或者不填表示不用插入Front-matter.
    //front-matter的具体配置见 front-matter.php 文件，可以自行调整
    'front-matter' => 'hexo',

    //是否使用github风格的code blocks的语法
    'github_code_block_style' => true
);