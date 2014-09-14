# Html2Markdown4Blog
一个专为blog制作的，将HTML形式的页面只保留文章内容，转换成Markdown格式的工具，目前仅支持CSDN Blog。
由于并没有针对转换后的排版做优化，所以要获得很好的浏览体验，可能还需要你做一些微调。
另外，目前还不支持表格的转换。

支持批量转换，输入目录页面的地址就可以转换所有的文章，能自动识别分页。
转换完成后会保存到本地目录。

## Requirements
* PHP Version >= 5
* 安装cURL扩展(注意,CLI和Apache下php.ini是不同的)

## Usage
```
git clone xxxx

cd xxxx
//编辑setting文件中的配置，填入要转换的目录页地址或单独的文章的地址

php start.php
```

