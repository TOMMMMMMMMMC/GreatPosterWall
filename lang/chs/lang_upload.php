<?php
$lang_upload = array(
    //classes/torrent_form.class.php
    'upload' => "发布",
    'upload_img' => "上传",
    'torrent_diff' => "本站禁止发布列表",
    'last_update' => "最后更新",
    'upload_note' => "请严格遵守站点 <a href='rules.php?p=upload' target='_blank'>发布规则</a>。禁止发布违规资源，欲了解更多信息请移步 <a href='wiki.php?action=article&name=手把手发种指南' target='_blank'>发种教程</a>。",
    'upload_rule' => "发布规则",
    'upload_denny' => "禁止发布违规资源，发种教程请移步",
    'upload_guide' => "发种教程",
    'show' => "显示",
    'hide' => "隐藏",
    'name' => "名称",
    'explain' => "注释",
    'show_more' => "显示更多",

    'personal_announce' => "你的个人 announce 地址是（请保密，不要泄露）",
    'personal_announce_note' => "为安全起见，已隐藏你的个人 announce 地址，请右键复制使用。",
    'torrent_file' => "种子文件",
    'json_file' => "JSON 文件 (选填)",
    'json_file_note' => "JSON 文件的作用请参阅 <a href='wiki.php?action=article&amp;id=17' target='_blank'>本文</a>",
    'type' => "分类",
    'artist' => "艺术家 (们)",
    'chinese_name' => "中文名",
    'artist_how_to_toggle' => " 如何填写艺术家名 ",
    'artist_how_to_blockquote' => "<strong class='important_text'>你必须完全掌握 <a href='wiki.php?action=article&amp;id=6' target='_blank'>发种命名规则</a>，此处仅摘要预览。</strong></br></br>
	<ol>
	<li>艺术家名（包括译名）必须使用官方名称，禁止自拟名（昵称、外号等）。</li>
	<li>华语艺术家必须用半角括号备注其英文名，没有官方英文名时须用拼音名替代。</li>
	<li>华语艺术家的拼音英文名，统一采用 “姓前名后” 的形式，姓和名的拼音要分开，且每个“字”的拼音首字母须大写，例：<a href='https://greatposterwall.com/artist.php?id=5307' target='_blank'>华晨宇 (Hua ChenYu)</a>。</li>
	<li>若艺术家某语种的名称不止一个，请选择最常用的作为该语种的名称。</li>
	<li>动漫、动画、游戏等原声专辑发行时，可能出现以虚拟人物作为专辑艺术家的情况（多见于此类日语专辑）。发布此类专辑时，如何设置艺术家的主要判断依据为人声是否为真。</li>
	<li>在艺术家编辑页面，可将其非官方名称（全名、本名、别名、昵称、外号等）设置为别名。具体设置方法请移步 <a href='https://greatposterwall.com/wiki.php?action=article&id=227' target='_blank'>编辑准则与惯例</a>。</li>
	<li>除极个别情况，“VA、Various Artists、群星” 不可作为艺术家名称，请使用 “+” 单独添加每一位艺术家。</li>
	</ol></br>
	示例：
	<div class='examples'>A. 华语艺术家（大陆、港澳台、新加坡等母语为华语的艺术家）：
	<ul><li><strong class=\"important_text\">必须</strong>备注英文名。无官方英文名时用拼音替代，具体要求请看下面说明。<ol><li>有英文名：<strong>中文名 (英文名)</strong>，<a href=\"artist.php?artistname=%E8%96%9B%E4%B9%8B%E8%B0%A6+%28Joker+Xue%29\" target='_blank'>薛之谦 (Joker Xue)</a></li><li>无英文名：<strong>中文名 (拼音名)</strong>，<a href=\"artist.php?artistname=%E8%B5%B5%E9%9B%B7+%28Zhao+Lei%29\" target='_blank'>赵雷 (Zhao Lei)</a>、<a href=\"artist.php?artistname=%E6%9D%8E%E8%8D%A3%E6%B5%A9+%28Li+RongHao%29\" target='_blank'>李荣浩 (Li RongHao)</a></li></ol></li></ol></li></ul></br>
	B. 欧美艺术家（欧美、澳洲等母语为英语的艺术家）：
	<ul><li>由于欧美艺术家的中文名基本是音译，故除被大家熟悉的艺术家可备注中文名外，其他不建议备注。<ol><li>无中文名：<strong>英文名</strong>，<a href=\"artist.php?artistname=Jason+Mraz\" target='_blank'>Jason Mraz</a></li><li>有中文名：<strong>英文名 (中文名)</strong>，<a href=\"artist.php?artistname=Backstreet+Boys+%28%E5%90%8E%E8%A1%97%E7%94%B7%E5%AD%A9%29\" target='_blank'>Backstreet Boys (后街男孩)</a></li></ol></li></ul></br>
	C. 他语艺术家（日、韩等母语为非中英文的艺术家）：
	<ul><li>英文名和本国名是最好的组合方式，且英文名须为主名称。少数大家熟悉的艺术家可考虑备注中文名。<ol><li>有英文名：<strong>英文名 (本国名)</strong>，<a href=\"artist.php?artistname=Mai+Kuraki+%28%E5%80%89%E6%9C%A8%E9%BA%BB%E8%A1%A3%29\" target='_blank'>Mai Kuraki (倉木麻衣)</a></li><li>无英文名：<strong>本国名</strong>，<a href=\"artist.php?artistname=Ang%C3%A8le\" target='_blank'>Angèle</a></li><li>有中、英文名：<strong>英文名 (本国名 / 中文名)</strong>，<a href=\"artist.php?artistname=Taeyeon+%28%EA%B9%80%ED%83%9C%EC%97%B0+%2F+%E9%87%91%E6%B3%B0%E5%A6%8D%29\" target='_blank'>Taeyeon (김태연 / 金泰妍)</a>、<a href=\"artist.php?artistname=Ayumi+Hamasaki+%28%E6%B5%9C%E5%B4%8E%E3%81%82%E3%82%86%E3%81%BF+%2F+%E6%BB%A8%E5%B4%8E%E6%AD%A5%29\" target='_blank'>Ayumi Hamasaki (浜崎あゆみ / 滨崎步)</a></li></ol></li></ul></div>",

    'torrent_rule' => "<h7>Please obey the <a href='wiki.php?action=article&amp;id=6' target='_blank'>Rules of Torrent Naming</a>. Please use “+” button on the right to add more artists, please check <a href='wiki.php?action=article&amp;id=17' target='_blank'>this instruction</a> for more information.</h7>",
    'artist_note' => "<strong class='important_text'>请采用右侧的多艺术家增删功能而非简单地将 “Various Artists” 作为一个艺术家添加，</strong>参阅 <a href='wiki.php?action=article&amp;id=128' target='_blank'>本文</a> 获取更多信息。",
    'main' => "主要",
    'guest' => "客座",
    'composer' => "作曲",
    'conductor' => "指挥",
    'dj_compiler' => "DJ／编曲",
    'remixer' => "重混",
    'producer' => "制作",
    'album' => "专辑标题",
    'title_how_to_toggle' => " 如何填写专辑标题 ",
    'title_how_to_blockquote' => "<strong>专辑标题：</strong>应客观反映专辑封面或侧脊上印刷的标题，如包含双语的官方标题，如中英、日英等，应按格式将两者都填写上。</br>
	<strong>中文标题：</strong>仅用于非华语专辑的官方中文译名备注，注意，它不是副标题，请不要将发行相关的说明信息填在这里。</br></br>
	<strong class='important_text'>你必须完全掌握 <a href='wiki.php?action=article&amp;id=6' target='_blank'>发种命名规则</a>，此处仅摘要预览。</strong></br></br>
	<ol>
	<li>专辑名称不论语种，必须使用官方名称。</li>
	<li>专辑标题中的符号须以官方名称为准，除某些语种的特殊情况外，应使用半角符号。</li>
	<li>专辑标题应使用与专辑发行名称相同的字体样式、符号样式以及排版。</li>
	</ol></br>
	示例：
	<div class='examples'>A. 华语艺术家专辑：
	<ul><li>不强制英文名，且只限官方英文名，不允许自译名。<ol><li>无英文名：<strong>中文名</strong>，<a href=\"/torrents.php?id=6863\" target='_blank'>大鱼</a></li><li>有英文名：<strong>中文名 (英文名)</strong>，<a href=\"/torrents.php?id=154\" target='_blank'>光冻 (Frozen Light)</a></li></ol></li></ul></br>
	B. 欧美艺术家专辑：
	<ul><li>专辑的中文名（多见于原声专辑）请添加到“中文标题”中，而非直接用括号备注。<ol><li>无中文名：<strong>英文名</strong>，<a href=\"/torrents.php?id=17295\" target='_blank'>Visions of Europe</a></li><li>有中文名：<strong>英文名 [中文标题]</strong>，<a href=\"/torrents.php?id=786\">The Lion King</a> [<a href=\"/torrents.php?searchstr=%E7%8B%AE%E5%AD%90%E7%8E%8B\" target='_blank'>狮子王</a>]</li></ol></li></ul></br>
	C. 他语艺术家专辑：
	<ul><li>英文名和本国名仍然是最佳的组合方式。另外，与欧美专辑一样，专辑的中文名须添加到 “中文标题” 中而非直接括号备注。<ol><li>有英文名：<strong>英文名 (本国名)</strong>，<a href=\"/torrents.php?id=17191\" target='_blank'>Kimi e no Tobira (キミヘノトビラ)</a>、<a href=\"/torrents.php?id=17350\" target='_blank'>Eight (에잇)</a></li><li>无英文名：<strong>本国名</strong>，<a href=\"/torrents.php?id=2875\" target='_blank'>Por Una Mujer</a>、<a href=\"/torrents.php?id=4942\" target='_blank'>さよならクロール</a></li><li>中文名和英文名都有：<strong>英文名 (本国名) [中文标题]</strong>，<a href=\"/torrents.php?id=16465\" target='_blank'>Signal OST (시그널)</a> [<a href=\"/torrents.php?searchstr=%E4%BF%A1%E5%8F%B7\" target='_blank'>信号</a>]</li><li>有中文名、无英文名：<strong>本国名 [中文标题]</strong>，<a href=\"/torrents.php?id=1522\" target='_blank'>透明な色</a> [<a href=\"/torrents.php?searchstr=%E9%80%8F%E6%98%8E%E8%89%B2\">透明色</a>]</li></ol></li></ul></div>",
    'album_note' => "<strong class='important_text'>注意：</strong>下面艺术家和标题的命名，请仔细阅读规则 <a href='rules.php?p=upload#h2.3' target='_blank'>2.3. 格式要求</a> 及 <a href='wiki.php?action=article&id=6' target='_blank'>发种命名规则</a>。",
    'chinese_title' => "中文标题",
    'chinese_title_note' => "仅用于非华语专辑的官方中文译名备注，不要将发行相关的说明信息填在这里。(如：金刚狼)",
    'musicbrainz' => "MusicBrainz",
    'musicbrainz_title' => "点击 “查询信息” 按钮，通过 MusicBrainz 的接口自动获取信息填充上传页面。",
    'musicbrainz_note' => "<input type='button' value='查询信息' id='musicbrainz_button' /> 使用 MusicBrainz 时，请仔细检查唱片厂牌和目录编号。更多信息，请参阅 <h7><a href='https://greatposterwall.com/wiki.php?action=article&id=41' target='_blank'>版本与发行指南</a></h7>。",
    'year' => "年份",
    'year_remaster' => "首次公映年份",
    'year_remaster_title' => "你为原始发行版指定的年份早于该媒介面世的时间。你需要填写发行信息，尤其是发行版年份。若你无法提供发行信息，请勾选下方的 “未知发行” 选框。",
    'year_remaster_note' => "专辑最早的发行年份。",
    'lable' => "注意：以下两处仅针对“原始发行版”填写。仅当你确定将发布版本为“原始发行版”，或如你知道该资源的“原始发行版”信息，才可填写！否则，请勾选下面的“版本信息”并填写相关信息。",
    'record_label' => "原始发行版的厂牌 (选填)",
    'record_label_note' => "唱片厂牌，一般来讲是唱片公司的宣传铭牌，非公司全称。更多信息请参考发种教程里面 <h7><a href='wiki.php?action=article&amp;id=17' target='_blank'>唱片厂牌</a></h7> 部分。",
    'catalogue_number' => "原始发行版的目录号 (选填)",
    'catalogue_number_note' => "目录号是发行单位对于专辑的唯一标识编号，多见于唱片包装的背面和侧面。",
    'releasetype_label' => "发行类别",
    'releasetype_label_note' => "请参阅 <a href='wiki.php?action=article&amp;id=10' target='_blank'>本指南</a> 或访问 <a href='https://musicbrainz.org/search' target='_blank'>MusicBrainz</a>、<a href='https://www.discogs.com/' target='_blank'>Discogs</a>、<a href='https://www.xiami.com/' target='_blank'>虾米音乐</a> 等信息站。",
    'edit' => "编辑",
    'group_info' => "版本信息",
    'remaster' => "所有任何版本（原版、重制版、豪华版、区域版等），请在下面填写相关发行信息，除发行年份以外，如不确定请留空。",
    'group_unknown' => "未知发行",
    'release_info_how_to_toggle' => " 如何填写发行信息 ",
    'release_info_how_to_blockquote' => "你可以通过查看<strong>专辑包装（侧脊、封底）、文件夹名、文件夹内的扫图、Log 文件中的内容等</strong>来比较可靠地确定以下信息。你可以借由 <a href='https://greatposterwall.com/forums.php?action=viewthread&threadid=251' target='_blank'>YADG</a> 搜索网络数据库来便捷地填写发行信息，但请注意，<strong class='important_text'>仅当你确定其所提供的信息切实符合你手头资源所属的发行时，才可填写获取自数据库的信息</strong>，仅仅曲目数量和播放顺序相同<strong class='important_text'>不足以</strong>作为发行一致的判据。</br></br>
	<ul>
	<li>未知发行：仅当你完全不确定全部发行信息（发行版年份、发行标题、唱片厂牌、目录编号）时，才勾选此项。</li>
	<li>发行版年份：此发行版的发行年份（若此为原始发行版，则与上面 “首次公映年份” 相同）。</li>
	<li>发行标题：如 “Deluxe Edition (豪华版)”、“Remastered (重灌版)” 等（如是原始发行应留空）。请不要将网络音乐商店的店名（如 Mora）填在这里。</li>
	<li>唱片厂牌：唱片厂牌，一般来讲是唱片公司的宣传铭牌，非公司全称。详情请参阅 <a href='https://greatposterwall.com/wiki.php?action=article&id=188' target='_blank'>唱片厂牌</a> 一文。</li>
	<li>目录编号：目录编号是发行单位对于专辑的唯一标识编号，多见于唱片包装的背面和侧面。详情请参阅 <a href='https://greatposterwall.com/wiki.php?action=article&id=190' target='_blank'>目录编号</a> 一文。</li></ul>",
    'group_unknown_label' => "请注意区分发行和版本的区别，参见 <a href='https://greatposterwall.com/wiki.php?action=article&id=140' target='_blank'>版本与发行指南</a>。",
    'existing_release_information' => "站点已有发行信息",
    'remaster_year' => "发行版年份",
    'remaster_year_note' => "此发行版的发行年份（若此为原始发行版，则与上面 “首次公映年份” 相同）。",
    'remaster_note' => "注意：以下三处（发行标题、唱片厂牌、目录编号）请尽可能填写，但若不确定，请留空。",
    'remaster_title' => "发行标题 (选填)",
    'remaster_title_note' => "如 “Deluxe Edition (豪华版)”、“Remastered (重灌版)” 等。(如是原始发行应留空)",
    'remaster_record_label' => "唱片厂牌 (选填)",
    'remaster_record_label_note' => "此版唱片厂牌。可能不同于原始发行版。",
    'remaster_catalogue_number' => "目录编号 (选填)",
    'remaster_catalogue_number_note' => "此版目录编号。可能不同于原始发行版。",
    'torrent_info_how_to_toggle' => " 如何填写以下信息 ",
    'torrent_info_how_to_blockquote' => "<ul>
	<li>Vanity House：仅在你发布自己的作品，或是代表某位艺术家发布时勾选，如此它才能算作 Vanity House 发行。勾选该选项也会自动将种子组添加到推荐。注意其与原创不同，它指的是自己或授权代表的歌曲作品，而非自购或自制资源。</li>
	<li>Scene：如果是你自己压制的，它就<span class='important_text'>不是</span>一个 “scene” 资源。如果你不确定就<span class='important_text'>不要</span>选择它；否则你将会被处罚。更多关于 “scene” 的信息，请参阅 <a href='https://greatposterwall.com/wiki.php?action=article&id=140' target='_blank'>Scene 指南</a>。</li>
	<li>媒介：指的是种子的初始来源。一些注意事项请参阅 <a href='https://greatposterwall.com/wiki.php?action=article&id=214' target='_blank'>Web 种子发布要点</a>。</li>
	</ul>",

    'scene' => "Scene",
    'scene_note_1' => "仅当确认为 “scene” 资源时才可选择此项。",
    'scene_note_2' => "如果是你自己压制的，它就<strong class='important_text'>不是</strong>一个 “scene” 资源。如果你不确定就<strong class='important_text'>不要</strong>选择它；否则你将会被处罚。更多关于 “scene” 的信息，请参阅 <a href='https://greatposterwall.com/wiki.php?action=article&id=140' target='_blank'>Scene 指南</a>。",
    'format' => "格式",
    'bitrate' => "比特率",
    'log' => "Log 文件",
    'log_note' => "上传前须 <a href='logchecker.php' target='_blank'>在此</a> 检查你的 Log。仅支持 EAC、XLD 的抓轨 Log，不要上传其他 .log 文件，其他 .log 文件请用 [hide] 标签添加到 “种子描述” 中。<br/>可在窗口内一次选取多个 Log 文件，也可点击 “<span class='brackets'>+</span>” 添加更多 Log 文件。",
    'formats' => "多格式同时发布",
    'add_torrent_description' => "添加种子描述",
    'extra_format' => "额外格式 ",
    'vh' => "Vanity House",
    'vh_note' => "仅当确认为 “Vanity House” 资源时才可选择此项。",
    'media' => "媒介",
    'media_note' => "<strong class='important_text'>注：</strong>WEB 指的是从在线商店购买的数字音频，而非从网络某处获取的资源。若不确定资源的媒介，请使用 Unkown Media（但注意 24 bit 无损禁止使用 Unknown Media 发布）。",
    'logcue' => "Log/cue",
    'logcue_log' => "若种子拥有，或应有 Log 文件，请勾选此项。",
    'logcue_cue' => "若种子拥有，或应有 Cue 文件，请勾选此项。",
    'bad_tags' => "问题标签",
    'bad_tags_note' => "若种子的标签存在问题，请勾选此项。",
    'bad_files' => "问题文件名",
    'bad_files_note' => "若种子的文件名存在问题，请勾选此项。",
    'bad_compress' => "问题压缩",
    'bad_compress_note' => "若种子文件的压缩存在问题，请勾选此项。",
    'bad_folders' => "问题文件（夹）名",
    'bad_folders_note' => "若种子的文件夹名存在问题，请勾选此项。",
    'bad_img' => "问题扫图",
    'bad_img_note' => "若种子的扫图存在问题，请勾选此项。",
    'missing_lineage' => "缺少来源信息",
    'missing_lineage_note' => "若种子缺少来源信息，请勾选此项。",
    'custom_trumpable' => "自定义可替代理由",
    'cassette_approved' => "批准磁带",
    'cassette_approved_note' => "若种子是受批准的磁带翻录，请勾选此项。",
    'lossymaster_approved' => "批准有损母带",
    'lossymaster_approved_note' => "若种子是受批准的有损母带，请勾选此项。",
    'lossyweb_approved' => "批准有损 WEB",
    'lossyweb_approved_note' => "若种子是受批准的 WEB 发行，请勾选此项。",
    'tags' => "标签",
    'tags_how_to_toggle' => " 如何填写标签 ",
    'tags_how_to_blockquote' => "</br>详情请参阅 <a href='https://greatposterwall.com/wiki.php?action=article&id=42' target='_blank'>标签使用规范</a>。",

    'marks' => "原创标记",
    'tag' => "标记",
    'zhu' => "<strong for='zhuyi' class='important_text'>注:</strong>",
    'buy' => "自购",
    'diy' => "自制",
    'jinzhuan' => "禁转",
    'allow' => "未允禁转",
    'marks_warning' => "<strong class='important_text'>以上所有标记仅适用于 “原创” 资源，转载资源不得使用，否则将导致你被警告。</strong>",
    'marks_how_to_toggle' => " 如何使用原创标记 ",
    'marks_how_to_blockquote' => "<ul>
	<li>标记解释：
		<ol class='postlist'>
			<li>自购：仅特指从在线商店购买的 WEB 资源。</li>
			<li>自制：由发布者本人将母碟（CD、SACD、黑胶等）提取数字格式的行为。如母碟并非自己所有，请在 “种子描述” 处说明。</li>
			<li>禁转：禁止转载到任何地方。</li>
			<li>未允禁转：转载前须经发布者同意。</li>
		</ol>
</li>
<li>自购和自制只可选其一；禁转和未允禁转只可选其一。</li>
<li>禁转（未允禁转）仅代表发布者个人意愿，对于资源会否被转载到其他地方，本站不做任何保证。</li></ul>",
    'tag_note' => "<li><strong class='important_text'>以上所有标签仅针对“原创”资源，转载资源不得使用，否则将导致你被警告。</strong>解释：
                    <ol class='postlist'>
                        <li>自购：仅特指从在线商店购买的WEB资源。</li>
                        <li>自制：由发布者本人将母碟（CD、SACD、黑胶等）提取数字格式的行为。如母碟并非自己所有，请在“种子描述”处说明。</li>
                        <li>禁转：禁止转载到任何地方。</li>
                        <li>未允禁转：转载前须发布者同意。</li>
                    </ol>
                </li>
                <li>自购和自制只可选其一；禁转和未允禁转只可选其一。</li>
                <li>禁转（未允禁转）仅代表发布者个人意愿，对于资源会否被转载到其他地方，本站不做任何保证。</li>",


    'image' => "封面链接",
    'image_host' => "图床",
    'image_placeholder' => "支持粘贴图片地址、本地图片拖至此处或点击右侧上传按钮",
    'image_how_to_toggle' => " 如何添加封面 ",
    'image_how_to_blockquote' => "请遵守 <a href='wiki.php?action=article&id=27' target='_blank'>封面说明</a>，推荐使用右侧的上传按钮上传到官方图床。
	<ul>
	<li>在保证可以顺利加载的前提下，可以使用外部链接，如虾米、网易，抑或是其他图床。<strong>请勿使用 Imgur 一类被 “墙” 的图床。</strong></li>
	<li>由于仅供预览，因此请不要使用尺寸过大的封面图，1000 像素见方已经足够清晰。</li>
	<li>同样是出于提高加载速度的考虑，请使用高压缩比的格式如 JPG，而尽量避免使用 PNG。</li>
	</ul>
	<strong class='important_text'>注：</strong>请尽量完善封面，若因偷懒大量发布缺少封面的种子，将会导致你受处罚。",
    'copied' => "已复制",
    'image_note' => "<h7>请遵守 <a href='wiki.php?action=article&id=27' target='_blank'>封面说明</a>，推荐使用右侧的上传按钮上传到官方图床。<span><strong class='important_text'>注：</strong>请尽量完善封面，若因偷懒大量发布缺少封面的种子，将会导致你受处罚。</span></h7>",
    'description_how_to_toggle' => " 如何填写专辑和种子描述 ",
    'description_how_to_blockquote' => "<ul>
	<li>专辑描述：专辑的简单信息、背景描述和曲目列表。最少也要填写 “曲目列表”。注意：请不要粘贴冗长的介绍，同时注意和 “种子描述” 相区分。更多信息请参阅 <a href='wiki.php?action=article&amp;id=17' target='_blank'>专辑描述</a>。</li>
	<li>种子描述：只针对此种子的描述。可以是此资源的编码信息、抓轨或转码工具设置、频谱图、来源说明、购买截图等一切针对该种子的信息。请注意和 “专辑描述” 相区分，并且<strong class='important_text'>不要在此粘贴抓轨日志</strong>。更多信息请参阅 <a href='wiki.php?action=article&amp;id=17' target='_blank'>种子描述</a>。</li></ul>",
    'groupdescription' => "专辑描述",
    'groupdescription_note' => "专辑的简单信息、背景描述和曲目列表。最少也要填写 “曲目列表”。注意：请不要粘贴冗长的介绍，同时注意和 “种子描述” 相区分。更多信息请参阅 <a href='wiki.php?action=article&amp;id=17' target='_blank'>专辑描述</a>。",
    'torrentdescription' => "种子描述 (选填)",
    'torrentdescription_note' => "只针对此种子的描述。可以是此资源的编码信息、抓轨或转码工具设置、频谱图、来源说明、购买截图等一切针对该种子的信息。请注意和 “专辑描述” 相区分，并且<strong class='important_text'>不要在此粘贴抓轨日志</strong>。更多信息请参阅看 <a href='wiki.php?action=article&amp;id=17' target='_blank'>种子描述</a>。",
    'freeleech' => "免费下载",
    'because' => "由于",



    'assurance' => "确保你的种子符合 <h7><a href='rules.php?p=upload' target='_blank'>发布规则</a></h7>。否则将会受到<strong class='important_text'>警告</strong>或<strong class='important_text'>处罚</strong>。",
    'assurance_note' => "<p>上传种子后，你将有一个小时的保护期，在此期间，除你之外没有人可以使用此种子应求，明智地利用这段时间，并搜索 “<a href='requests.php' target='_blank'>求种列表</a>”。</p>",
    'torrent_rule' => "<h7>除极个别情况，“VA、Various Artists、群星” 不可作为艺术家名称，请使用 “+” 编辑具体艺术家。更多信息请参阅 <a href='wiki.php?action=article&amp;id=17' target='_blank'>此说明</a>。</h7>",


    #upload_handle
    'select_a_type' => "请选择一个有效分类。",
    'title_length_limit' => "标题长度应在 1 到 200 个字符之间",
    'original_release_year_must_be_entered' => "必须填写首次公映年份",
    'select_a_valid_release_type' => "必须选择有效发行类别",
    'at_least_one_tag' => "必须填写一个有效标签，长度上限为 200 字符",
    'album_description_min_10' => "专辑描述的长度应大于 10 个字符。",
    'selected_year_earlier_than_media_was_created' => "你填写的年份早于该媒介面世的年份：",
    'remaster_year_must_be_entered' => "必须填写母带重灌／再版的发行年份。",
    'invalid_remaster_year' => "无效的母带重灌发行年份。",
    'remaster_title_length_limit' => "发行标题长度应在 2 到 80 个字符之间。",
    'original_release_not_valid' => "\"原始发行\" 是无效的发行标题。",
    'record_label_length_limit' => "唱片厂牌长度应在 2 到 80 个字符之间。",
    'catalogue_number_length_limit' => "目录编号长度应在 2 到 80 个字符之间。",
    'select_valid_format' => "请选择有效的格式。",
    'flac_must_lossless' => "FLAC 的比特率一定是无损的。",
    'enter_the_other_bitrate' => "你必须填写其他比特率（上限：9 字符）。",
    'choose_a_bitrate' => "你必须选择一个比特率。",
    'select_a_media' => "请选择一个有效媒介。",
    'invalid_img_url' => "你填写的图片链接是无效的。",
    'release_description_length_limit' => "种子描述的长度应大于 10 个字符。",
    'group_id_not_numeric' => "Group ID 不是数值型",
    'title_length_limit' => "标题长度必应在 2 到 200 个字符之间。",
    'release_year_must_be_entered' => "必须填写发行年份。",
    'torrent_must_abide_rules' => "你的种子必须遵守规则。",
    'no_torrent_uploaded' => "种子未上传，或上传了一个空文件。",

    'enter_a_audiobook_description' => "你必须填写正确的有声书描述。",
    'not_torrent_file' => "你上传的文件似乎并不是种子。",
    'period' => "。",
    'no_extra_torrent_uploaded' => "额外种子未上传，或上传了一个空文件。",
    'torrents_entered_twice' => "一个或更多种子被上传了两次。",
    'extra_format_missing' => "额外种子未指定格式。",
    'extra_bitrate_missing' => "额外种子未指定比特率。",
    'enter_at_least_one_artist' => "请填写至少一位导演。",
    'same_torrent_exists' => "站点已存在完全相同的种子文件！",
    'thank_you_fix_torrent' => "感谢你修复这个种子！",
    'not_supported_encrypted_file_list' => "该种子包含本站不支持的加密文件列表。",
    'name_too_long' => "种子包含了文件名超长的文件：<br />",
    'torrents_contain_not_supported_encrypted_file_list' => "至少一个种子包含本站不支持的加密文件列表。",
    'splitted_eac_range_rip' => "EAC 整轨方式抓取的分轨资源",
    'cant_copy_log' => "未成功将 Log 文件复制到服务器。",

    'warning' => "注意",
    'header_warning' => "注意",
    'upload_handle_warning' => "注意",
    'need_download_new_torrent1' => "你的种子已经上传成功；但你需要从 ",
    'here' => "这里",
    'need_download_new_torrent2' => " 重新下载种子并开始做种。",

    //static/functions/validate_upload.js
    'a_main_artist_is_required' => "至少要有一个主要艺术家",

















    //movie_tracker
    'movie_type' => "片种",
    'drama_type' => "剧种",
    'movie_upload_note' => "<strong class='important_text'>注意：</strong>下面艺术家和标题的命名，请仔细阅读规则 <a href='rules.php?p=upload#h2.1' target='_blank'>2.1. 命名</a> 及 <a href='wiki.php?action=article&id=17' target='_blank'>发种命名规则</a>。",
    'movie_title' => "电影标题",
    'movie_title_placeholder' => "请输入电影的英文标题",
    'movie_aliases' => "中文标题",
    'movie_aliases_note' => "请输入电影的中文标题。如果你想要填写他语译名，请点击右侧的 “+” 按钮增添一栏，一栏限填一个。",

    'director' => "导演",
    'writer' => "编剧",
    'movie_producer' => "制片",
    'cinematographer' => "摄影",
    'actor' => "演员",

    'movie_cover' => "电影封面",
    'trailer_link' => "预告片链接",
    'trailer_link_placeholder' => "http://video.mtime.com/12345/?mid=123456",
    'movie_remaster_year' => "发行版年份 (选填)",
    'movie_remaster_year_note' => "如为原版，请填写与原始发行相同的年份。",
    'not_main_movie' => "非电影主体",
    'not_main_movie_label' => "当且仅当种子内不包含电影主体时勾选此项。",
    'not_main_movie_note' => "例：仅包含额外内容、Rifftrax、工作样片。",
    'movie_edition_information' => "版本信息",
    'movie_edition_information_label' => "如果种子来自特定的版本，请勾选此项。",
    'movie_edition_information_examples' => "例：珍藏集的一部分、特殊版本或非同寻常的特点。<a href=\"wiki.php?action=article&name=版本信息填写指南\">点此</a> 阅读版本指南。<strong>当选择 “电影大师” “标准收藏” “华纳档案馆” “4K修复版” “4K重制版” “重制版” 或自定义版本信息时，强烈建议填写该版本所对应的年份。</strong>",
    'movie_information' => "具体信息",
    'collections' => "珍藏集",
    'masters_of_cinema' => "电影大师",
    'the_criterion_collection' => "标准收藏",
    'warner_archive_collection' => "华纳档案馆",
    'editions' => "版本",
    'director_s_cut' => "导演剪辑版",
    'extended_edition' => "加长版",
    'rifftrax' => "Rifftrax",
    'theatrical_cut' => "影院版",
    'uncut' => "未删减版",
    'unrated' => "未分级版",
    'features' => "特点",
    '2_disc_set' => "双碟套装",
    '2_in_1' => "二合一",
    '2d_3d_edition' => "2D/3D版",
    '3d_anaglyph' => "红蓝3D",
    '3d_full_sbs' => "全宽3D",
    '3d_half_ou' => "半高3D",
    '3d_half_sbs' => "半宽3D",
    '4k_restoration' => "4K修复版",
    '4k_remaster' => "4K重制版",
    'remaster' => "重制版",
    '10_bit' => "10-bit",
    'dts_x' => "DTS:X",
    'dolby_atmos' => "杜比全景声",
    'dolby_vision' => "杜比视界",
    'dual_audio' => "双音轨",
    'english_dub' => "英语配音",
    'extras' => "额外内容",
    'hdr10' => "HDR10",
    'hdr10plus' => "HDR10+",
    'remux' => "Remux",
    'with_commentary' => "评论音轨",
    'clear' => "清除",
    'other' => "其他",
    'year' => "年份",

    'movie_scene' => "Scene",
    'movie_scene_label' => "当且仅当它是 “scene release” 时勾选此项。如果它是你自购自制的，那么它就不是一个 scene release。",
    'movie_scene_note' => "你可以前往 <a href=\"https://pre.corrupt-net.org/\" target=\"_blank\">pre.corrupt-net.org</a> 或 <a href=\"https://www.srrdb.com/\" target=\"_blank\">srrDB</a> 搜索文件名再次确认。",

    'movie_subtitles' => "字幕",
    'chinese_simplified' => "简体",
    'chinese_traditional' => "繁体",
    'simplified_and_traditional' => "简繁",
    'english' => "英语",
    'japanese' => "日语",
    'korean' => "韩语",
    'show_more' => "显示更多",

    'no_subtitles' => "无字幕",
    'arabic' => "阿拉伯语",
    'brazilian_port' => "巴西葡萄牙语",
    'bulgarian' => "保加利亚语",
    'croatian' => "克罗地亚语",
    'czech' => "捷克语",
    'danish' => "丹麦语",
    'dutch' => "荷兰语",
    'estonian' => "爱沙尼亚语",
    'finnish' => "芬兰语",
    'french' => "法语",
    'german' => "德语",
    'greek' => "希腊语",
    'hebrew' => "希伯来语",
    'hindi' => "印地语",
    'hungarian' => "匈牙利语",
    'icelandic' => "冰岛语",
    'indonesian' => "印度尼西亚语",
    'italian' => "意大利语",
    'latvian' => "拉脱维亚语",
    'lithuanian' => "立陶宛语",
    'norwegian' => "挪威语",
    'persian' => "波斯语",
    'polish' => "波兰语",
    'portuguese' => "葡萄牙语",
    'romanian' => "罗马尼亚语",
    'russian' => "俄语",
    'serbian' => "塞尔威亚语",
    'slovak' => "斯洛伐克语",
    'slovenian' => "斯洛文尼亚语",
    'spanish' => "西班牙语",
    'swedish' => "瑞典语",
    'thai' => "泰语",
    'turkish' => "土耳其语",
    'ukrainian' => "乌克兰语",
    'vietnamese' => "越南语",
    'movie_subtitles_note' => "<strong>注意：</strong>强制字幕和硬编码字幕是两个不同的概念。",

    'movie_production_company' => "出品公司",
    'movie_specifics' => "详情",
    'movie_source' => "片源",
    'movie_codec' => "编码",
    'movie_container' => "容器",
    'movie_resolution' => "分辨率",
    'auto_detect' => "*自动检测",
    'movie_processing' => "处理",
    'encode' => "Encode",
    'remux' => "Remux",
    'diy' => "DIY",
    'untouched' => "Untouched",
    'movie_group' => "组 (选填)",
    'movie_imdb' => "IMDb",
    'no_imdb_link' => "无 IMDb 链接",
    'imdb_empty_warning' => "如 IMDb 链接确实不存在，请勾选选框，否则无法发布。",
    'no_imdb_note' => "请务必确认 IMDb 链接确实不存在，否则你可能会收到警告。",
    'movie_douban' => "豆瓣",
    'chinese_movie_synopsis' => "中文影片简介",
    'english_movie_synopsis' => "英文影片简介",
    'staff_note' => "管理备注",
    'mediainfo_bdinfo_placeholder' => "请在此粘贴 MediaInfo/BDInfo 全文，每框限填一段",
    'mediainfo_bdinfo_note' => "如果有多个视频文件或多张碟片，请使用右上角的 “+” 按钮分别添加各自所属的 MediaInfo/BDInfo。",
    'movie_torrent_description' => "种子描述",
    'movie_torrent_description_placeholder' => "种子描述",
    'movie_torrent_description_note' => "我们要求你至少提供三张 PNG 截图（<a href=\"wiki.php?action=article&name=原始分辨率截图指南\" target=\"_blank\">指南</a>）和一份完整的 MediaInfo（<a href=\"wiki.php?action=article&name=MediaInfo+使用指南\" target=\"_blank\">指南</a>）或 BDInfo（<a href=\"wiki.php?action=article&name=BDInfo+使用指南\" target=\"_blank\">指南</a>）日志。<br/>建议将文本说明放在前面，截图放在后面，特效字幕截图和画面截图分开，参考 <a href=\"torrents.php?torrentid=1\" target=\"_blank\">此种子</a>。<br/>有关必需信息的更多规定，请参阅我们的 <a href=\"rules.php?p=upload\" target=\"_blank\">规则页面</a>。",
    'movie_fill' => "自动填充",
    'nfo' => "NFO 文件",

    'reprint_rules' => "转载规则",
    'self_purchase' => "自购",
    'self_rip' => "自制",
    'movie_feature' => "特色槽选项",
    'movie_feature_note' => "<strong>注意：</strong>以上选项仅适用于外语电影（粤语等方言不被视为外语）。",
    'chinese_dubbed_label' => "国语配音",
    'special_effects_subtitles_label' => "特效字幕",
    'movie_trumpable' => "可替代标记",
    'no_sub' => "缺少基本字幕",
    'hardcode_sub' => "硬字幕",
    'mixed_subtitles' => "内封字幕",

    'preview' => "预览",
    'dead_torrent' => "死种",
);
