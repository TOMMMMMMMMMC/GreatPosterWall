<?
/*
 * The $Types array is the backbone of the reports system and is stored here so it can
 * be included on the pages that need it without clogging up the pages that don't.
 * Important thing to note about the array:
 *   1. When coding for a non music site, you need to ensure that the top level of the
 * array lines up with the $Categories array in your config.php.
 *   2. The first sub array contains resolves that are present on every report type
 * regardless of category.
 *   3. The only part that shouldn't be self-explanatory is that for the tracks field in
 * the report_fields arrays, 0 means not shown, 1 means required, 2 means required but
 * you can't select the 'All' box.
 *   4. The current report_fields that are set up are tracks, sitelink, link and image. If
 * you wanted to add a new one, you'd need to add a field to the reportsv2 table, elements
 * to the relevant report_fields arrays here, add the HTML in ajax_report and add security
 * in takereport.
 *   5. Modified by ZexWoo for matching a movie tracker.
 */

$ReportCategories = [
    'master' => 'General',
    '1' => 'Movie',
    '2' => 'Application',
    '3' => 'E-Book',
    '4' => 'Audiobook',
    '5' => 'E-Learning Video',
    '6' => 'Comedy',
    '7' => 'Comics',
    '8' => 'Music',
];

$Types = array(
    'master' => array(
        'dupe' => array(
            'priority' => '10',
            'reason' => '0',
            'title' => '重复',
            'report_messages' => array(
                '请附上既有种子的链接。'
            ),
            'report_fields' => array(
                'sitelink' => '1'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]h2.2[/rule]。你的种子已被报告，因为它与站点既有种子重复。'
            )
        ),
        'banned' => array(
            'priority' => '230',
            'reason' => '14',
            'title' => '特别禁止内容',
            'report_messages' => array(
                '请明确指出其违反了 “禁止发布” 列表中的哪一项。'
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '4',
                'delete' => '1',
                'pm' => '[rule]h1.2[/rule]。你上传了本站目前禁止的资源。列于禁止发布列表（位于 [url=' . site_url() . 'upload.php]发布页面[/url] 顶部）以及上传规则中 [url=' . site_url() . 'rules.php?p=upload#h1.2]特别禁止[/url] 部分的资源不能被上传到本站。除非你的种子符合禁止发布列表注释中指定的条件，否则请勿发布。
                你的种子已被报告，因为它包含了来自禁止发布列表或上传规则中特别禁止部分的资源。'
            )
        ),
        'urgent' => array(
            'priority' => '280',
            'reason' => '-1',
            'title' => '紧急',
            'report_messages' => array(
                '该类目仅适用于紧急情况，一般是因为在种子中泄露了个人信息。',
                '滥用 “紧急” 类目会导致警告或更严重的惩罚。',
                '由于该类目不能方便地告知管理员问题所在，所以请在说明中详细描述种子的问题。'
            ),
            'report_fields' => array(
                'sitelink' => '0',
                // 'track' => '0',
                'link' => '0',
                'image' => '0',
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => ''
            )
        ),
        'other' => array(
            'priority' => '250',
            'reason' => '-1',
            'title' => '其他',
            'report_messages' => array(
                '请在说明中尽可能详细地描述问题。'
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => ''
            )
        ),
        'trump' => array(
            'priority' => '20',
            'reason' => '1',
            'title' => '替代',
            'report_messages' => array(
                '请列出新种子能够替代原有种子的具体原因。',
                '请确保你正在报告的，是<strong class="important_text">将被替代</strong>且应被删除的种子，而不是你认为应保留的种子。'
            ),
            'report_fields' => array(
                'sitelink' => '1'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]h2.2[/rule]。你的种子已被报告，因为它将被新的种子替代。'
            )
        )
    ),
    '1' => array( //Movie Resolves
        'transcode' => array(
            'priority' => '100',
            'reason' => '9',
            'title' => '劣质转码',
            'report_messages' => array(
                "请写明你检查的音视频轨以及用以确认它为劣质转码的方法。",
                "如有可能，请附上至少一张用以佐证的分析图，分析图多多益善。"
            ),
            'report_fields' => array(
                'image' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '2',
                'delete' => '1',
                'pm' => '[rule]5.4.21[/rule]。我们不接受有损来源的转码或重编码。
                你的种子已被报告，因为它包含了劣质转码的音轨。'
            )
        ),
        'low' => array(
            'priority' => '90',
            'reason' => '8',
            'title' => '劣质源',
            'report_messages' => array(
                "请向我们提供 PNG 格式的影片原始分辨率截图。"
            ),
            'report_fields' => array(
                'image' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]5.4.10[/rule]、[rule]5.4.11[/rule]、[rule]5.4.21[/rule]、[rule]5.4.24[/rule]。编码自质量低下的、存在错误的、低清的源的作品会被标记为 “可替代”。'
            )
        ),

        'names_bad' => array(
            'priority' => '30',
            'reason' => '2',
            'title' => '问题文件（夹）名',
            'report_messages' => array(
                "请指出有问题的文件（夹）名。",
                "理想情况下，你可以发布修复了文件（夹）名问题之后的种子以替代该种子。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => "[rule]2.1.1[/rule]。文件（夹）名必须使用电影原始语种名称或官方英文名（推荐）。（如海报所示等的官方英文名，其优先级高于 IMDb。）
                
                [rule]2.1.2[/rule]。压制组发行（来自 P2P 组或 Scene 组）不应重命名，除非它们不满足规则 [rule]2.1.1[/rule] 或我们的文件名要求。

                [rule]2.1.4[/rule]。DVD 和 BD 文件目录结构不允许改动，仅顶层文件夹允许重命名。
                
                你的种子被标记为问题文件（夹）名且可被替代。当然你也可自行修复这个种子，补充或修正文件（夹）名然后重新发布种子。然后以 “替代” 为由报告（RP）旧种，在报告说明中指出你已修复的命名问题，同时请确保提供用以替代的新种永久链接（PL）。"
            )
        ),
        'video_track_bad' => array(
            'priority' => '40',
            'reason' => '3',
            'title' => '问题视频轨',
            'report_messages' => array(
                "请指出视频轨存在的具体问题，并向我们提供 PNG 格式的影片原始分辨率截图。"
            ),
            'report_fields' => array(
                'image' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => "[rule]5.4.1[/rule]、[rule]5.4.2[/rule]。种子的纵横比因编码错误等原因与原始的、影院上映的电影不同。
                
                [rule]5.4.5[/rule]、[rule]5.4.6[/rule]。种子被错误地反交错，或以错误的帧率播放。

                [rule]5.4.18[/rule]。种子明显裁边过多或过少。

                [rule]5.4.22[/rule]。种子含有明显的水印。
                
                你的种子被标记为 “问题视频轨” 且可被替代。"
            )
        ),
        'audio_track_bad' => array(
            'priority' => '50',
            'reason' => '4',
            'title' => '问题音频轨',
            'report_messages' => array(
                "请指出音频轨存在的具体问题。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => "[rule]5.4.3[/rule]。种子内含的音频轨相对其分辨率而言过于庞大。

                [rule]5.4.4[/rule]。种子含有冗余的音频轨。
                
                [rule]5.4.16[/rule]。既没有原始语种音频也没有国语配音，只有非国语配音的种子会被标记为 “可替代”。
                
                [rule]5.4.17[/rule]。种子中包含的音轨有效，但不同步。
                
                你的种子被标记为 “问题音频轨” 且可被替代。"
            )
        ),
        'subtitle_track_bad' => array(
            'priority' => '60',
            'reason' => '5',
            'title' => '问题字幕轨',
            'report_messages' => array(
                "请指出字幕轨存在的具体问题。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => "[rule]5.4.7[/rule]。种子中包含的字幕有效，但不同步。
                
                [rule]5.4.14[/rule]。无中字槽位的种子如果是不包含英文字幕（通过字幕管理器内挂或外挂）的非英语电影，会被标为 “可替代”。

                [rule]5.4.15[/rule]。无中字槽位的种子，其重要非英语对白不包含单独的英文字幕。

                [rule]5.4.19[/rule]。包含字幕质量很差，且不是电影准确翻译的种子会被标为 “可替代”。

                [rule]5.4.20[/rule]。字幕被硬编码在视频轨中的种子会被标为 “可替代”。
                
                你的种子被标记为 “问题字幕轨” 且可被替代。"
            )
        ),
        'torrent_description_bad' => array(
            'priority' => '80',
            'reason' => '7',
            'title' => '问题种子描述',
            'report_messages' => array(
                "请指出种子描述存在的具体问题。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => "[rule]2.2[/rule]。当前填写的种子描述未符合规则要求，可能是 MediaInfo 不完整、有错误；或截图不符合规则。"
            )
        ),
        'format' => array(
            'priority' => '70',
            'reason' => '6',
            'title' => '规格不当',
            'report_messages' => array(
                "在规则容许规格范围外的种子都可以使用本项，如 SDR x265 1080p Encode、RMVB 720p Encode 等。",
                "请指明是容器不被规则允许，还是分辨率存在问题。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]5.4.8[/rule]。种子应符合我们 [rule]h3[/rule] 推荐格式的要求。
                
                [rule]5.4.9[/rule]。种子应符合我们 [rule]h3[/rule] 推荐分辨率的要求。
                
                你的种子已被报告，因为其格式或分辨率与规则不符。'
            )
        )


        // 'upload_contest' => array(
        //     'priority' => '163',
        //     'reason' => '-1',
        //     'title' => '请求批准上传比赛',
        //     'report_messages' => array(
        //         '请附上带有写着你用户名小纸条的实体贴片照片。',
        //         '<strong class="important_text">任何你提交用作证据的图片只有管理组成员可见。</strong>'
        //     ),
        //     'report_fields' => array(
        //         'proofimages' => '2'
        //     ),
        //     'resolve_options' => array(
        //         'upload' => '0',
        //         'warn' => '0',
        //         'delete' => '0'
        //     )
        // )
    ),
    '8' => array( //Music Resolves
        'tag_trump' => array(
            'priority' => '50',
            'reason' => '4',
            'title' => '替代问题标签',
            'report_messages' => array(
                '请列出新种子能够替代原有种子的具体有哪个或哪些元数据标签。',
                '请确保你正在报告的，是<strong class="important_text">将被替代</strong>且应被删除的种子，而不是你认为应保留的种子。'
            ),
            'report_fields' => array(
                'sitelink' => '1'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]2.3.16[/rule]。请正确地为你的音乐文件添加元数据标签。准确的元数据标签（例如 ID3、Vorbis）在所有上传中都有要求。请确保你为你的文件所选择的标签格式是正确的（例如 FLAC 不能使用 ID3 标签——参照[rule]2.2.10.8[/rule]）。相较于 ID3v1，我们大力推荐 ID3v2 标签。 对于 AC3 种子，ID3 是受推荐的，但并不强制，因为 AC3 格式并不原生支持文件元数据标签（于 AC3 而言，文件名才是正确标记媒体文件的工具）。上传同时具有正确的 ID3v1 标签和空白的 ID3v2 标签的种子（两种标签的集合）是可以被替代的，其替代品可以是含正确 ID3v1 标签的种子，也可以是含正确 ID3v2 标签的种子（单一标签的集合）。如果你上传了一个缺失一类或更多必需标签的专辑，其他用户就能够下载、补充标签，重新上传并报告你的种子以便替代并删除。
                你的种子已被报告，因为它将被元数据标签优化过的种子替代。'
            )
        ),
        'vinyl_trump' => array(
            'priority' => '60',
            'reason' => '1',
            'title' => '替代低质量黑胶',
            'report_messages' => array(
                '请列出新种子能够替代原有种子的具体原因。',
                '<strong class="important_text">请尽你所能地详尽描述细节。请参考特定的曲目和时间位置以佐证你的报告。</strong>',
                '请确保你正在报告的，是<strong class="important_text">将被替代</strong>且应被删除的种子，而不是你认为应保留的种子。'
            ),
            'report_fields' => array(
                'sitelink' => '1'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]2.5.5[/rule]。黑胶抓轨可以被听感更好的同位深翻录替代，无论频谱信息如何（见 [rule]2.3.9[/rule]）。
                你的种子已被报告，因为它将被听感更好的黑胶翻录替代。'
            )
        ),
        'folder_trump' => array(
            'priority' => '40',
            'reason' => '3',
            'title' => '替代问题文件夹名',
            'report_messages' => array(
                '请列出存在问题的文件夹名。',
                '请确保你正在报告的，是<strong class="important_text">将被替代</strong>且应被删除的种子，而不是你认为应保留的种子。'
            ),
            'report_fields' => array(
                'sitelink' => '1'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]2.3.2[/rule]。使用有意义的标题命名文件目录，例如 “艺术家 - 专辑名 (年) [格式]”。我们允许的最低限度是 “专辑名”，但最好是能包含更多信息。如果文件目录不满足该最低标准，另一个用户可以下载、重命名、再上传并报告你的种子以供替代并删除。此外，若种子文件夹以 Scene 的习惯命名且种子本身没加 Scene 标签，则它将是可替代的。
                [rule]2.3.3[/rule]。避免在正确命名的目录下创建不必要的嵌套文件夹（例如将专辑文件放入一个额外的文件夹）。带有不必要嵌套文件夹的种子可被移除嵌套文件夹的种子替代。对于多碟专辑，种子的主文件夹可能包含多个用于保存碟盒中单张碟内容的子文件夹，例如，种子的主文件夹为 “Adele - 19 (2008) [FLAC]”，其子文件夹可能包含 “19 (Disc 1of2)” 或 “19” 以及 “Live From The Hotel Cafe (Disc 2of2)” 或 “Acoustic Set Live From The Hotel Cafe, Los Angeles” 的字样。如无必要，勿增实体，如果你对于其他的情形一筹莫展，就请 PM 管理组寻求帮助吧。
                你的种子已被报告，因为它将被文件夹名和目录结构优化过的种子替代。'
            )
        ),
        'file_trump' => array(
            'priority' => '30',
            'reason' => '2',
            'title' => '替代问题文件名',
            'report_messages' => array(
                '请列出存在问题的文件名。',
                '请确保你正在报告的，是<strong class="important_text">将被替代</strong>且应被删除的种子，而不是你认为应保留的种子。'
            ),
            'report_fields' => array(
                'sitelink' => '1'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]2.3.11[/rule]。文件名必须正确反映曲目名。你可别把文件命名为 “音轨 01.mp3、音轨 02.mp3……” 这样的东西。包含不正确曲目名文件的种子可被命名正确的种子替代。同时，源自 Scene 却未添加 Scene 标签的种子必须符合站点的命名规则（文件名中不得含有发布组名、文件名中不得含有广告等）。如果曲目名中所有的字母都大写，则该种子可被替代。如果你上传了一张带有错误文件名的专辑，其他用户就可以下载、修复文件名、再上传并报告你的种子以供替代删除。
                你的种子已被报告，因为它将被文件名优化过的种子替代。'
            )
        ),
        'tracks_missing' => array(
            'priority' => '240',
            'reason' => '15',
            'title' => '音轨（曲目）不全',
            'report_messages' => array(
                '请列出丢失音轨的曲目名和曲目号。',
                '如有可能，请提供 Amazon.com 或其他源的链接以方便查看正确的曲目列表。'
            ),
            'report_fields' => array(
                'track' => '2',
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]2.1.19[/rule]。所有的音乐种都必须包含一个完整的发行，不可以缺失音轨（若是多碟发行，则不能缺失碟片）。
                [rule]2.1.19.2[/rule]。单个音轨（例如单个 MP3 文件）不可以被上传，除非它是官方发行的单曲专辑。如果某个特定音轨只能在某个专辑上找到，你必须将整个专辑制成种子上传。
                你的种子已被报告，因为它缺失音轨。'
            )
        ),
        'discs_missing' => array(
            'priority' => '120',
            'reason' => '6',
            'title' => '多碟专辑碟片不全',
            'report_messages' => array(
                '如有可能，请提供 Amazon.com 或其他源的链接以方便查看正确的曲目列表。'
            ),
            'report_fields' => array(
                'track' => '0',
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]2.1.19[/rule]。所有的音乐种都必须包含一个完整的发行，不可以缺失音轨（若是多碟发行，则不能缺失碟片）。
                [rule]2.1.19.1[/rule]。若某个专辑以 CD 或黑胶的多碟集合的形式发行（或是盒装），它必须被制成单个种子上传。若要精益求精，就将每个多碟中每个独立的碟片包含的内容放进其专属的文件夹中（见 [rule]2.3.12[/rule]）。
                你的种子已被报告，因为它缺失碟片。'
            )
        ),
        'mqa' => array(
            'priority' => '130',
            'reason' => '14',
            'title' => '禁止 MQA',
            'report_messages' => array(
                '请通过截屏证明文件是由 MQA 编码的（除非在版本描述中明确显示了）。'
            ),
            'extra_log' => 'MQA-encoded torrent',
            'report_fields' => array(
                'image' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]1.2.9[/rule]。你上传了本站目前禁止的资源。MQA 编码的 FLAC 种子在' . SITE_NAME . '是不被允许的，更多内容请参见 [[MQA]]。'
            )
        ),
        'bonus_tracks' => array(
            'priority' => '90',
            'reason' => '-1',
            'title' => '只包含特典音轨（曲目）',
            'report_messages' => array(
                '如有可能，请提供 Amazon.com 或其他源的链接以方便查看正确的曲目列表。',
                '根据 <a href="rules.php?p=upload#r2.4.5" target="_blank">规则 2.4.5</a>，WEB 源独占的特典音轨可以被单独上传。'
            ),
            'report_fields' => array(
                'track' => '0',
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]2.1.19.3[/rule]。特典光盘根据 [rule]h2.4[/rule] 可被单独上传。但请注意，单独的特典音轨是不能脱离专辑的剩余部分而上传的，因为特典音轨和特典光盘不是一码事。带有数据或视频轨的增强型音乐 CD 须排除非音频轨上传。如果你想要分享其中的视频或数据，你可以通过网盘等方式分享并在种子描述中添加分享链接。
                你的种子已被报告，因为它只包含特典音轨且非完整专辑。'
            )
        ),
        'transcode' => array(
            'priority' => '250',
            'reason' => '16',
            'title' => '劣质转码',
            'report_messages' => array(
                "请写明你检查的音轨以及用以确认转码的方法。",
                "如有可能，请附上至少一张频谱分析图，分析图多多益善。"
            ),
            'report_fields' => array(
                'image' => '0',
                'track' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '2',
                'delete' => '1',
                'pm' => '[rule]2.1.2[/rule]。我们不接受有损来源的转码或重编码。
                你的种子已被报告，因为它包含了劣质转码的音频文件。'
            )
        ),
        'low' => array(
            'priority' => '170',
            'reason' => '10',
            'title' => '低比特率',
            'report_messages' => array(
                "请告诉我们你用以查看比特率的软件以及文件的真实比特率。"
            ),
            'report_fields' => array(
                'track' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '2',
                'delete' => '1',
                'pm' => '[rule]2.1.3[/rule]。无论格式为何，音乐资源的平均比特率至少要达到 192 kbps。例外：下列 VBR 编码可以低于该限制：LAME V2 (VBR)、V1 (VBR)、V0 (VBR)、APS (VBR)、APX (VBR)、MP3 192 (VBR) 和 AAC ~192 (VBR) 到 AAC ~256 (VBR) 的资源。
                你的种子已被报告，因为它包含了一个或更多未达到最小比特率要求的音频文件。'
            )
        ),
        'mutt' => array(
            'priority' => '180',
            'reason' => '11',
            'title' => '有损混编',
            'report_messages' => array(
                "请列出至少两个具有不同比特率和／或编码的音轨。"
            ),
            'report_fields' => array(
                'track' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '2',
                'delete' => '1',
                'pm' => '[rule]2.1.6[/rule]。音乐种子中的所有音频文件都应以相同设置的同一编码器编码。
                你的种子已被报告，因为它包含一个或更多由不同音频编码器或不同编码器设置编码的音频文件。'
            )
        ),
        // 'single_track' => array(
        //     'priority' => '270',
        //     'reason' => '18',
        //     'title' => '整轨抓轨专辑',
        //     'report_messages' => array(
        //         "如有可能，请提供 Amazon.com 或其他源的链接以方便查看正确的曲目列表。",
        //         "该选项用于报告本应分轨抓取的 CD（即 CD 中的曲目是分立的）以整轨形式抓取的情况。",
        //         "请不要将该选项与音轨缺失相混淆，上传多首曲目之一是为缺失，而将多首曲目作为单一文件抓取并上传才可用该选项。"
        //     ),
        //     'report_fields' => array(
        //         'link' => '0'
        //     ),
        //     'resolve_options' => array(
        //         'upload' => '0',
        //         'warn' => '1',
        //         'delete' => '1',
        //         'pm' => '[rule]2.1.5[/rule]。专辑不可以整轨形式抓取。
        //         [rule]2.1.5.1[/rule]。如果音轨在原始 CD 中就是分立的，你必须按照分轨形式抓取它们。任何缺失 .cue 文件的 FLAC 整轨翻录都会被立即删除。任何包含 .cue 文件的 FLAC 整轨翻录可以被正确分割的 FLAC 种子替代。只含有一个音轨的 CD 可在不预先分割的情况下上传。
        //         你的种子已被报告，因为它包含整轨翻录而非分轨翻录。'
        //     )
        // ),
        'tags_lots' => array(
            'priority' => '82',
            'reason' => '4',
            'title' => '问题／缺少元数据标签',
            'report_messages' => array(
                "请指出具体缺失了哪个元数据标签以及是否在所有音轨中都缺失了。",
                "理想情况下，你可以将修复了标签问题的资源重新上传并以 “替代问题标签” 为由替代该资源。"
            ),
            'report_fields' => array(
                'track' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => "[rule]2.3.16[/rule]。完善你音乐文件的元数据标签。
                上传规则要求所有上传的资源都带有正确的元数据标签。
                
                你的种子被标记为问题标签，同时它被列在 [url=" . site_url() . "better.php]站点优化[/url] 页面且可被替代。当然你也可自行修复这个种子，补充或修正所需要的元数据标签然后上传到站点即可。然后以 “替代问题标签” 为由报告（RP）旧种，在报告说明中指出你已修复的标签问题，同时请确保提供用以替代的新种永久链接（PL）。"
            )
        ),
        'folders_bad' => array(
            'priority' => '81',
            'reason' => '3',
            'title' => '问题文件夹名',
            'report_messages' => array(
                "请指出有问题的文件夹名。",
                "理想情况下，你可以将修复了文件夹名问题的资源重新上传并以\"Bad Folder Name Trump\"为由替代该资源。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => "[rule]2.3.2[/rule]。请以有意义的方式命名你的目录，例如“艺术家 - 专辑名 (年) [格式]”。
                上传规则要求所有上传的资源的目录名都是有意义的。
                
                你的种子被标记为问题文件夹名，同时它被列在 [url=" . site_url() . "better.php]站点优化[/url] 页面且可被替代。当然你也可自行修复这个种子，补充或修正文件夹／目录名然后重新上传种子。然后以“替代问题文件夹名”为由报告（RP）旧种，在报告说明中指出你已修复的文件夹名问题，同时请确保提供用以替代的新种永久链接（PL）。"
            )
        ),
        'wrong_format' => array(
            'priority' => '320',
            'reason' => '20',
            'title' => '指定格式错误',
            'report_messages' => array(
                "请告知我们正确的格式。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]2.1.4[/rule]。比特率须正确反映编码器设置或是音频文件的平均比特率。你有责任在上传页面上提供正确的格式和比特率信息。
                你的种子现已标记上了正确的格式和比特率信息。'
            )
        ),
        'wrong_media' => array(
            'priority' => '330',
            'reason' => '21',
            'title' => '指定媒介错误',
            'report_messages' => array(
                "请告知我们正确的媒介。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => ''
            )
        ),
        'format' => array(
            'priority' => '100',
            'reason' => '5',
            'title' => '不允许格式',
            'report_messages' => array(
                "如有可能，请列出相关音轨。"
            ),
            'report_fields' => array(
                'track' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]2.1.1[/rule]。本站允许的音频格式只有：
                有损：MP3、AAC、AC3、DTS
                无损：FLAC、WAV
                你的种子已被报告，因为它包含了不允许格式的音频文件。'
            )
        ),
        'bitrate' => array(
            'priority' => '150',
            'reason' => '9',
            'title' => '比特率不正确',
            'report_messages' => array(
                "请告知我们真实的比特率以及你用以查看的软件。",
                "若其真实比特率导致该种子成为重复资源（应被替代），请将之以重复为由报告，并在 “说明” 中描述具体情况。",
                "若其真实比特率导致该种子能够替代另一资源，请将之以替代为由报告，并在 “说明” 中描述具体情况。"
            ),
            'report_fields' => array(
                'track' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]2.1.4[/rule]。比特率须正确反映编码器设置或是音频文件的平均比特率。你有责任在上传页面上提供正确的格式和比特率信息。
                你的种子已被报告，因为它其中的一个或多个音频文件的比特率被篡改过。'
            )
        ),
        'source' => array(
            'priority' => '210',
            'reason' => '12',
            'title' => '电台／电视／调频／网络录制',
            'report_messages' => array(
                "请为证明该报告提供尽可能详细的信息。"
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '2',
                'delete' => '1',
                'pm' => '[rule]2.1.11[/rule]。翻录自无线电（卫星或调频）、电视、网络或播客的音乐是不被允许的。
                我们允许的媒介格式只有 CD、DVD、黑胶、Soundboard、SACD、DAT、磁带、WEB 和蓝光。'
            )
        ),
        'discog' => array(
            'priority' => '130',
            'reason' => '7',
            'title' => '自编合辑（多专辑合集）',
            'report_messages' => array(
                "请为证明该报告提供尽可能详细的信息。"
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]2.1.20[/rule]。用户自编的合辑是不允许上传的。在任何情况下，多专辑种子在本站都是不被允许的。也就是说，自编合辑、捆绑零售等都不行。如果几张专辑（例如单曲 CD）未曾以合辑的形式发行过，你就不能把它们打包上传。现场调音台的资源须以每晚、每场演出或每个会场为单位制成一个种子上传。包含多于一场演出的单个种子会成为多专辑种子。
                你的种子已被报告，因为它由非官方集合的多个独立专辑组成。'
            )
        ),
        'user_discog' => array(
            'priority' => '290',
            'reason' => '19',
            'title' => '自编集锦（多音轨合集）',
            'report_messages' => array(
                "请为证明该报告提供尽可能详细的信息。"
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]2.1.16[/rule]。用户自编的集锦是不允许上传的。
                [rule]2.1.16.1[/rule]。这些被定义为由不能代表官方艺术家或厂牌的上传者或其他人汇编的集锦。集锦须是官方合理发布的。用户自编和多种非官方渠道混音都是不被允许的。
                你的种子已被报告，因为它是用户自编集锦。'
            )
        ),
        'lineage' => array(
            'priority' => '190',
            'reason' => '-1',
            'title' => '无来源信息',
            'report_messages' => array(
                "请指出种子具体缺失了什么来源信息（硬件、软件等）。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]2.3.9[/rule]。所有的无损模拟源翻录应包含明确的来源信息。所有无损格式的 SACD 数字层的模拟翻录以及黑胶翻录必须包含所使用的录音设备的明确信息（见 [rule]h2.8[/rule]）。如果你在翻录黑胶时使用了 USB 转盘，请在来源信息中写明这一点。同时，请包含制作无损编码的所有中间步骤，例如用以制作母带的程序、所使用的声卡等等。缺失翻录信息的无损模拟翻录可以被同等或更优质量的包含更详尽翻录信息的无损模拟翻录替代。若要替代无来源信息的无损模拟翻录，用以替代的新种子须包括以 .txt 或 .log 格式存储的来源信息。
                你的种子现在可以被一个来源信息完整且音质更优的翻录替代。'
            )
        ),
        'edited' => array(
            'priority' => '140',
            'reason' => '8',
            'title' => '人为改动 Log',
            'report_messages' => array(
                "请解释你因何原因认为 Log 文件被人为改动过。",
                "该选项不会导致该种子在种子页显示 “已报告”，但请放心，管理员会看到这条报告的。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '4',
                'delete' => '1',
                'pm' => '[rule]2.2.10.9[/rule]。Log 文件决不允许编辑。
                [rule]2.2.10.9.1[/rule]。伪造 Log 数据是对质量的严重歪曲，且在证据确凿的情况下会导致被警告以及失去上传权限。我们建议你无论如何都不要打开 Log 文件。不过，如果你非要打开，也请不要出于任何理由编辑任何信息。如果你发现软件设置得不对，你必须以正确设置重新抓轨。在任何情况下都不要合并原本分开的 Log 文件。如果你必须要重抓某张碟子中的特定音轨，且由此得来的新 Log 的内容恰好跟在原 Log 后面，那就随它去吧，千万别从中删除任何内容，也不要从新 Log 中复制粘贴一部分到原 Log
                你的种子已被报告，因为它包含了被编辑过的 Log（无论是你还是别人动的手）。如果对于你的上传权限有任何疑问，请务必私信处理该 Log 状况的那位管理组成员。'
            )
        ),
        'audience' => array(
            'priority' => '70',
            'reason' => '22',
            'title' => '现场观众录音',
            'report_messages' => array(
                "请为证明该报告提供尽可能详细的信息。"
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]2.1.12[/rule]。非官方的现场观众录音是不被允许的。这包括但不限于 AUD（观众录音）、IEM（入耳式监听器）、ALD（听力辅助装置）、Mini-Disc 和 Matrix-sourced（矩阵录音设备）（见 [rule]2.6.3[/rule]）。
                你的种子已被报告，因为它来自于现场观众录音。'
            )
        ),
        'filename' => array(
            'priority' => '80',
            'reason' => '2',
            'title' => '问题文件名',
            'report_messages' => array(),
            'report_fields' => array(
                'track' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]2.3.11[/rule]。文件名必须正确反映曲目名。你可别把文件命名为 track 01.mp3、track 02.mp3……这样的东西。包含不正确曲目名文件的种子可被命名正确的种子替代。同时，源自 Scene 却未添加 Scene 标签的种子必须符合站点的命名规则（文件名中不得含有发布组名、文件名中不得含有广告等）。如果曲目名中所有的字母都大写，则该种子可被替代。
                [rule]2.3.13[/rule]。文件名要求有音轨号（例如 “01 - TrackName.mp3”）。如果一个包含有文件名缺失音轨号文件的种子被上传，则它可被文件名包含音轨号的种子替代。在按正确格式命名后，文件就能按照音轨号或播放顺序排好队了。你也可以看看 [rule]2.3.14[/rule]。
                
                上传规则要求所有上传的音轨都应包含正确的文件名。你的种子现因包含错误或不完整的文件名被标记为问题文件名，同时它被列在 [url=' . site_url() . 'better.php]站点优化[/url] 页面且可被替代。当然你也可自行修复这个种子，补充或修正文件名重新上传种子。然后以 “替代问题文件名” 为由报告（RP）旧种，在报告说明中指出你已修复的文件名问题，同时请确保提供用以替代的新种永久链接（PL）。'
            )
        ),
        'img_trump' => array(
            'priority' => '65',
            'reason' => '24',
            'title' => '替代问题扫图',
            'report_messages' => array(
                '请指出有问题的扫图。',
                '请确保你正在报告的，是<strong class="important_text">将被替代</strong>且应被删除的种子，而不是你认为应保留的种子。'
            ),
            'report_fields' => array(
                'sitelink' => '1'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '包含错误扫图，或单个扫图文件体积过大，或扫图文件总体积相较于种子体积占比过大（这并非指不建议包含完整的小册子扫图，仅指扫图文件的体积问题）。具体请查阅 Wiki 中的 [url=https://greatposterwall.com/wiki.php?action=article&id=58]扫图规则[/url]。
                你的种子已被报告，因为它将被扫图质量更好的种子替代。'
            )
        ),
        'compress_trump' => array(
            'priority' => '66',
            'reason' => '22',
            'title' => '替代未按规范压缩的 FLAC',
            'report_messages' => array(
                '请指出压缩不规范的文件。',
                '请确保你正在报告的，是<strong class="important_text">将被替代</strong>且应被删除的种子，而不是你认为应保留的种子。'
            ),
            'report_fields' => array(
                'sitelink' => '1'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]2.2.10.10[/rule]。FLAC 音乐文件必须压缩。我们强烈推荐将 FLAC 音乐压缩到 Level 8。没有压缩会增加至少 5% 的体积。未经压缩的种子都可能会被报告并替代。
                你的种子已被报告，因为它将被规范压缩的种子替代。'
            )
        ),
        'img_bad' => array(
            'priority' => '87',
            'reason' => '23',
            'title' => '问题扫图',
            'report_messages' => array(
                "请指出有问题的扫图。",
                "理想情况下，你可以将修复了扫图问题（通常是扫图过大）的资源重新上传并以 “替代问题扫图” 为由替代该资源。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '包含错误扫图，或单个扫图文件体积过大，或扫图文件总体积相较于种子体积占比过大（这并非指不建议包含完整的小册子扫图，仅指扫图文件的体积问题）。具体请查阅 Wiki 中的 [url=https://greatposterwall.com/wiki.php?action=article&id=58]扫图规则与[/url]。
                
                你的种子现因问题扫图被列在 [url=' . site_url() . 'better.php]站点优化[/url] 页面且可被替代。当然你也可自行修复这个种子，补充或修正扫图重新上传种子。然后以 “替代问题扫图” 为由报告（RP）旧种，在报告说明中指出你已修复的扫图问题，同时请确保提供用以替代的新种永久链接（PL）。'
            )
        ),
        'compress_bad' => array(
            'priority' => '88',
            'reason' => '22',
            'title' => '未按规范压缩的 FLAC',
            'report_messages' => array(
                "请指出压缩不规范的文件。",
            ),
            'report_fields' => array(
                'track' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]2.2.10.10[/rule]。FLAC 音乐文件必须压缩。我们强烈推荐将 FLAC 音乐压缩到 Level 8。没有压缩会增加至少 5% 的体积。未经压缩的种子都可能会被报告并替代。

                你的种子现因问题压缩被列在 [url=' . site_url() . 'better.php]站点优化[/url] 页面且可被替代。当然你也可自行修复这个种子，规范压缩后重新上传种子。然后以 “替代未规范压缩的无损” 为由报告（RP）旧种，在报告说明中指出你已修复的压缩问题，同时请确保提供用以替代的新种永久链接（PL）。'
            )
        ),
        'skips' => array(
            'priority' => '220',
            'reason' => '13',
            'title' => '段落丢失／编码错误',
            'report_messages' => array(
                '<strong class="important_text">请尽你所能详细彻底地描述详情。指出特定的音轨和时间点来佐证你的报告。</strong>'
            ),
            'report_fields' => array(
                'track' => '2'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]2.1.8[/rule]。若非源自黑胶，则音乐文件绝不可带有噼啪声、咔哒声或是遗漏。如果被报告翻录／编码错误，它们就会被删除。

                你的种子已被报告，因为它的一个或多个音轨包含编码错误。'
            )
        ),
        'rescore' => array(
            'priority' => '160',
            'reason' => '-1',
            'title' => '请求重新计算 Log 得分',
            'report_messages' => array(
                "如果你描述了具体理由，可以帮助我们理解为什么你认为这份 Log 应重新计算得分。",
                "例如，应被打分的外语 Log，或是根本就没上传的 Log。"
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]2.2.10.3[/rule]。在 Log checker 中评分 100% 的 EAC 或 XLD 抓轨可以替代较低得分的种子。注意：由于 [url=' . site_url() . 'wiki.php?action=article&amp;id=51]本 wiki[/url] 所述内容，因未禁用音频缓存而得分 95% log 的种子可能可以重新打分到 100%。

                [rule]2.2.10.5[/rule]。非英文或中文的 XLD 和 EAC log 需要请求管理组手动调整分数。

                [rule]2.2.10.9.2[/rule]。如果你发现合并 Log 没有被正确评分，请以 “请求重新计算 Log 得分” 为由报告该种子。
                你的种子现已被管理组正确调分。'
            )
        ),
        'lossyapproval' => array(
            'priority' => '161',
            'reason' => '-1',
            'title' => '请求批准有损母带',
            'report_messages' => array(
                '请包含尽可能多的信息来佐证报告，包括频谱分析图。',
                '对于 CD 或其他物理媒介，请附上带有写着你用户名小纸条的专辑的照片。',
                '<strong class="important_text">任何你提交用作证据的图片只有管理组成员可见。</strong>'
            ),
            'report_fields' => array(
                'proofimages' => '2'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]2.1.2.2[/rule]。[important]官方发行的有损发行不被视为转码，且可以上传。[/important]更多信息请参阅 [url=' . site_url() . 'wiki.php?action=article&amp;id=64]本文[/url]。'
            )
        ),
        'lossywebapproval' => array(
            'priority' => '162',
            'reason' => '-1',
            'title' => '请求批准有损 WEB',
            'report_messages' => array(
                '请包含尽可能多的信息来佐证报告，包括频谱分析图。',
                '请附上网络商店的购买链接以及你的会员身份截图或购买单据截图。',
                '<strong class="important_text">任何你提交用作证据的图片只有管理组成员可见。</strong>'
            ),
            'report_fields' => array(
                'proofimages' => '2'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0',
                'pm' => '[rule]2.1.2.2[/rule]。[important]官方发行的有损发行不被视为转码，且可以上传。[/important]更多信息请参阅 [url=' . site_url() . 'wiki.php?action=article&amp;id=64]本文[/url]。
                
                [rule]2.1.2.3[/rule]。[important]来自 Bandcamp 和 Beatport 等类似零售商，且由艺术家或唱片公司发行的有损发行视为官方的有损发行。[/important]任何来源的无损发行可以替代 Web 源的有损发行。如果同一家网络零售商更新了发行且随后提供了无损发行的下载，新版可替代旧版。'
            )
        ),
        'upload_contest' => array(
            'priority' => '163',
            'reason' => '-1',
            'title' => '请求批准上传比赛',
            'report_messages' => array(
                '请附上带有写着你用户名小纸条的专辑的照片。',
                '<strong class="important_text">任何你提交用作证据的图片只有管理组成员可见。</strong>'
            ),
            'report_fields' => array(
                'proofimages' => '2'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '0'
            )
        )
    ),

    '2' => array( //Applications Rules Broken
        'missing_crack' => array(
            'priority' => '70',
            'reason' => '-1',
            'title' => 'No Crack/Keygen/Patch',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。',
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]4.1.2[/rule]. All applications must come with a crack, keygen, or other method of ensuring that downloaders can install them easily. App torrents with keygens, cracks, or patches that do not work or torrents missing clear installation instructions will be deleted if reported. No exceptions.
Your torrent was reported because it was missing an installation method.'
            )
        ),
        'game' => array(
            'priority' => '50',
            'reason' => '-1',
            'title' => 'Game',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。',
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '4',
                'delete' => '1',
                'pm' => '[rule]1.2.5[/rule]. Games of any kind. No games of any kind for PC, Mac, Linux, mobile devices, or any other platform are allowed.
[rule]4.1.7[/rule]. Games of any kind are prohibited (see [rule]1.2.5[/rule]).
Your torrent was reported because it contained a game disc rip.'
            )
        ),
        'free' => array(
            'priority' => '40',
            'reason' => '-1',
            'title' => 'Freely Available',
            'report_messages' => array(
                'Please include a link to a source of information or to the freely available app itself.',
            ),
            'report_fields' => array(
                'link' => '1'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]4.1.3[/rule]. App releases must not be freely available tools. Application releases cannot be freely downloaded anywhere from any official source. Nor may you upload open source applications where the source code is available for free.
Your torrent was reported because it contained a freely available application.'
            )
        ),
        'description' => array(
            'priority' => '80',
            'reason' => '-1',
            'title' => 'No Description',
            'report_messages' => array(
                'If possible, please provide a link to an accurate description.',
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]4.1.4[/rule]. Torrent descriptions for applications must contain good information about the application. You should either have a small description of the program (either taken from its web site or from an NFO file) or a link to the information&#8202;&mdash;&#8202;but ideally both. Torrents missing this information will be deleted when reported.
Your torrent was reported because it lacked adequate release information.'
            )
        ),
        'pack' => array(
            'priority' => '20',
            'reason' => '-1',
            'title' => 'Archived Pack',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。'
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]2.1.18[/rule]. Sound Sample Packs must be uploaded as applications.
[rule]4.1.9[/rule]. Sound sample packs, template collections, and font collections are allowed if they are official releases, not freely available, and unarchived. Sound sample packs, template collections, and font collections must be official compilations and they must not be uploaded as an archive. The files contained inside the torrent must not be archived so that users can see what the pack contains. That means if sound sample packs are in WAV format, they must be uploaded as WAV. If the font collection, template collection, or sound sample pack was originally released as an archive, you must unpack the files before uploading them in a torrent. None of the contents in these packs and collections may be freely available.
Your torrent was reported because it was an archived collection.'
            )
        ),
        'collection' => array(
            'priority' => '30',
            'reason' => '-1',
            'title' => 'Collection of Cracks',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。'
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]4.1.11[/rule]. Collections of cracks, keygens or serials are not allowed. The crack, keygen, or serial for an application must be in a torrent with its corresponding application. It cannot be uploaded separately from the application.
Your torrent was reported because it contained a collection of serials, keygens, or cracks.'
            )
        ),
        'hack' => array(
            'priority' => '60',
            'reason' => '-1',
            'title' => 'Hacking Tool',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。',
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]4.1.12[/rule]. Torrents containing hacking or cracking tools are prohibited.
Your torrent was reported because it contained a hacking tool.'
            )
        ),
        'virus' => array(
            'priority' => '60',
            'reason' => '-1',
            'title' => 'Contains Virus',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。 Please also double-check that your virus scanner is not incorrectly identifying a keygen or crack as a virus.',
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]4.1.14[/rule]. All applications must be complete.
The torrent was determined to be infected with a virus or trojan. In the future, please scan all potential uploads with an antivirus program such as AVG, Avast, or MS Security Essentials.
Your torrent was reported because it contained a virus or trojan.'
            )
        ),
        'notwork' => array(
            'priority' => '60',
            'reason' => '-1',
            'title' => 'Not Working',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。',
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]4.1.14[/rule]. All applications must be complete.
This program was determined to be not fully functional.
Your torrent was reported because it contained a program that did not work or no longer works.'
            )
        )
    ),

    '3' => array( //Ebook Rules Broken
        'unrelated' => array(
            'priority' => '270',
            'reason' => '-1',
            'title' => 'Ebook Collection',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。'
            ),
            'report_fields' => array(),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]6.5[/rule]. Collections/packs of ebooks are prohibited, even if each title is somehow related to other ebook titles in some way. All ebooks must be uploaded individually and cannot be archived (users must be able to see the ebook format in the torrent).
Your torrent was reported because it contained a collection or pack of ebooks.'
            )
        )
    ),

    '4' => array( //Audiobook Rules Broken
        'skips' => array(
            'priority' => '210',
            'reason' => '13',
            'title' => 'Skips / Encode Errors',
            'report_messages' => array(
                '<strong class="important_text">Please be as thorough as possible and include as much detail as you can. Refer to specific tracks and time positions to justify your report.</strong>'
            ),
            'report_fields' => array(
                'track' => '2'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '0',
                'delete' => '1',
                'pm' => '[rule]2.1.8[/rule]. Music not sourced from vinyl must not contain pops, clicks, or skips. They will be deleted for rip/encode errors if reported.
Your torrent was reported because one or more audiobook tracks contain encoding errors.'
            )
        )
    ),

    '5' => array( //E-Learning videos Rules Broken
        'dissallowed' => array(
            'priority' => '20',
            'reason' => '-1',
            'title' => 'Disallowed Topic',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。'
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]7.3[/rule]. Tutorials on how to use musical instruments, vocal training, producing music, or otherwise learning the theory and practice of music are the only allowed topics. No material outside of these topics is allowed. For example, instruction videos about Kung Fu training, dance lessons, beer brewing, or photography are not permitted here. What is considered allowable under these topics is ultimately at the discretion of the staff.
Your torrent was reported because it contained a video that has no relevance to the allowed music-related topics on the site.'
            )
        )
    ),

    '6' => array( //Comedy Rules Broken
        'talkshow' => array(
            'priority' => '270',
            'reason' => '-1',
            'title' => 'Talkshow/Podcast',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。'
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '1',
                'delete' => '1',
                'pm' => '[rule]3.3[/rule]. No radio talk shows or podcasts are allowed. Those recordings do not belong in any torrent category.
Your torrent was reported because it contained audio files sourced from a talk show or podcast.'

            )
        )
    ),

    '7' => array( //Comics Rules Broken
        'titles' => array(
            'priority' => '180',
            'reason' => '-1',
            'title' => 'Multiple Comic Titles',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。'
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '',
                'delete' => '1',
                'pm' => '[rule]5.2.3[/rule]. Collections may not span more than one comic title. You may not include multiple, different comic titles in a single collection, e.g., "The Amazing Spider-Man #1" and "The Incredible Hulk #1."
Your torrent was reported because it contained comics from multiple unrelated series.'
            )
        ),
        'volumes' => array(
            'priority' => '190',
            'reason' => '-1',
            'title' => 'Multiple Volumes',
            'report_messages' => array(
                '请为证明该报告提供尽可能详细的信息。'
            ),
            'report_fields' => array(
                'link' => '0'
            ),
            'resolve_options' => array(
                'upload' => '0',
                'warn' => '',
                'delete' => '1',
                'pm' => '[rule]5.2.6[/rule]. Torrents spanning multiple volumes are too large and must be uploaded as separate volumes.
Your torrent was reported because it contained multiple comic volumes.'
            )
        )
    )
);
