# 获取影视信息
## Request
* Method: GET
* URL: /upload.php?action=movie_info&imdbid={imdb_id}&check_only={bool}
## Response
> Body(JSON)

参数名称 |类型 |描述 | 例
:---- |:- |:- |:- 
code| int | 错误码  | 1, 2, 3, 4
response | object | 返回数据
error | string | 错误描述
### Success
#### 请求中`check_only=false`或不传
> 每个字段都可能为空

参数名称 |类型 |描述 | 例
:---- |:- |:- |:-
Title | string | 标题 |
Plot |string  | 介绍 |
Poster |string | 封面 |
ReleaseDate|string | 发行日期 | "24 Nov 2017"
Year | string | 年份 | "2020"
Runtime | string | 时长 | "112min"
IMDBRating | string | IMDB评分 | "7.4"
Region | string | 地区 | "China, Taiwan"
Language | string | 语言 | "Chinese, English"
Genre | string| 风格Tag | "Drama, Mystery, Thriller"
Type | string| 类型 方便区分长片短片| "movie"或"short"
RTRating |string | 烂番茄评分 | "70%"
SubTitle |string | 中文标题 |
Directors | dict | 导演, key: imdbid, value: english name | {nm3127578: "Ya-che Yang"}
Writters |dict | 编剧 key: imdbid, value: english name | {nm3127578: "Ya-che Yang"}
Casts |dict | 演员 key: imdbid, value: english name | {nm3127578: "Ya-che Yang"}
RestCasts | dict | 未在Credit中的演员，我们统一做演员处理 key: imdbid, value: english name | {nm3127578: "Ya-che Yang"}
Producers | dict | 制片 key: imdbid, value: english name | {nm3127578: "Ya-che Yang"}
Composers | dict | 作曲 key: imdbid, value: english name | {nm3127578: "Ya-che Yang"}
Cinematographers | dict | 摄影 key: imdbid, value: english name | {nm3127578: "Ya-che Yang"}
DoubanID |string | 豆瓣ID | "27113517"
ChineseName |dict | 艺人中文名字，key: english name value: chinese name | {Ya-che Yang: "杨雅喆"}
#### 请求中`check_only=true`
参数名称 |类型 |描述 | 例
:---- |:- |:- |:-
GroupID | int | 种子组的ID | 1
Dupe | bool | 是否重复 | false
### 错误码
Code | 描述
:- | :-
0 | 无错误 
1 | imdbid填写有误
2 | 已经有存在的group_id，`check_only=false`时不会返回该错误
3 | 未知错误，可能ID不正确或者服务出问题
        