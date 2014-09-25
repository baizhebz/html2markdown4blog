<?php
/**
 * YAML front-matter
 * Write the front-matter in YAML format. Don’t use tabs in the front-matter,
 * use spaces instead. Also, add a space after colons.
 *
 * 目前支持配置的变量有：
 * title      : 文章标题
 * date       : 文章日期
 * tags       : 文章的标签
 * categories : 文章的分类
 *
 * 你也可以定制自己需要的格式
 */

$jekyll = <<<JEKYLL
---
layout: post
title: { title }
date: { date }
categories: [{ categories }]
tags: [{ tags }]
---

JEKYLL;

$hexo = <<<HEXO
title: { title }
date: { date }
categories:
- { categories }
tags:
- { tags }
---

HEXO;

//可以自定义日期格式，参照php date()函数给出的format字符
$date = 'Y-m-d H:i:s';

return array(
    'jekyll' => $jekyll,
    'hexo' => $hexo,
    'date_format' => $date
);
