# Html2Markdown4Blog
##Blog迁移
这是一个专为 HTML to Markdown 形式的Blog迁移而制作的工具。
它能将HTML格式的页面只保留文章内容后，再转换成Markdown格式。注意目前仅支持**CSDN Blog**。

由于目前并没有针对转换后的排版做很多优化(而且CSDN编辑器生成的格式又挺乱的..)，所以要获得很好的浏览体验的话，可能还需要你手动做一些微调。
此外，目前并不支持表格的转换。

##批量转换
支持批量转换，输入目录页面的地址就可以转换所有的文章，能自动识别分页。
转换完成后会保存到本地目录。

##Front-matter
支持添加Jekyll(Octopress),Hexo或自定义的YAML front-matter

## Requirements
* PHP Version >= 5
* 安装cURL扩展(注意,CLI和Apache下php.ini是不同的)
* 如果文章过多可能造成执行时间较长，需注意max_execution_time的配置

## Usage
```
//git clone 或 下载
git clone https://github.com/baizhebz/html2markdown4blog.git

cd html2markdown4blog

//编辑setting.php文件中的配置，填入要转换的目录页地址或单独的文章的地址

//在CLI模式下运行，或者部署到你的web server，使用curl或直接在浏览器里访问 your_host/start.php 文件
php start.php
```

