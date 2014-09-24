<?php
return array(
    //md文件存储的路径
    'save_path' => BASE_PATH . 'posts',

    //目前仅支持csdn blog，这一项不用修改
    'blog_base_url' => 'http://blog.csdn.net',

    //目录列表页的url，一般设置成 http://blog.csdn.net/your_name 就可以了（注意不可以是博客管理中的文章管理列表）
    //如果设置了这一项，则下面的post_url设置无效
    'archive_url' => '',

    //某一篇文章的url，上面的archive_url未设置时，该设置有效
    'post_url' => '',

    //文件头部加入哪种YAML front-matter，可选的有：Jekyll(Octopress),Hexo
    //也可以填none或者不填表示不用插入Front-matter.
    //front-matter的具体配置见 front-matter.php 文件，可以自行调整
    'front-matter' => 'Jekyll',

    //是否使用github风格的code blocks的语法，即以在代码块开头和结尾加上```来替换掉在每行开头插入制表符的方式
    'github_code_block_style' => true
);