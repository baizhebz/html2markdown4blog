# Html2Markdown4Blog
##Blog迁移
这是一个将传统博客系统迁移到用Markdown格式书写的静态博客系统（例如Jekyll）的工具。
它能识别并截取HTML页面的文章内容，再转换成Markdown格式的文件。
注意目前仅支持**CSDN Blog**。

由于目前并没有针对转换后的排版做很多优化(而且CSDN编辑器生成的格式又挺乱的..)，所以要获得很好的浏览体验的话，可能还需要你手动做一些微调。

##批量转换
支持批量转换，输入目录页面的地址就可以转换所有的文章，能自动识别分页。
转换完成后会保存到本地目录。

##Front-matter
支持添加Jekyll(Octopress),Hexo或自定义的YAML front-matter

## Requirements
* PHP Version >= 5
* cURL PHP Extension (请注意运行环境，CLI和Apache下的php.ini可能是不同的)
* 如果文章过多可能造成执行时间较长，需注意max_execution_time的配置

## Usage
```
//git clone 或 下载: https://github.com/baizhebz/html2markdown4blog/archive/master.zip
git clone https://github.com/baizhebz/html2markdown4blog.git

cd html2markdown4blog

//编辑setting.php文件中的配置，修改archive_url或post_url，填入要转换的目录页地址或一篇单独的文章的地址

//在CLI模式下运行，或者部署到你的web server，使用curl或直接在浏览器里访问 your_host/start.php
php start.php
```

