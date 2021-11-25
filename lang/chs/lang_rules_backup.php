<?php
$site_name = SITE_NAME;
$staffpm = '<a href="staffpm.php">Staff PM</a>';
$lang_rules = array(
    'rules' => "规则",
    'type' => "分类",
    'info' => "注释",
    'golden_rules' => "黄金规则",
    'golden_rules_used' => "黄金规则适用于 Great Poster Wall 和我们的社交网络。这是最高级的规则；不遵守将会危害你的账号。",
    'short_11' => "禁止重复注册。",
    'long_11' => "每位用户终生仅允许拥有一个账号，如果你的账号被禁，你可以通过交流群或 ${staffpm} 联系管理员。不要创建多个账号（即俗称的 “马甲”）。一经查实所有关联账号都将被封禁。",
    'short_12' => "禁止交易、出售、赠与或提供账号。",
    'long_12' => "如果你想要封存账号, 可以通过 ${staffpm} 联系管理员来停用你的账号。",
    'short_13' => "禁止共享账号。",
    'long_13' => "账号仅供私人使用。禁止以任何方式（例如共享登录信息、外部程序等）将你的账号访问权限授予他人。如果你的亲朋好友想要使用本站，请 <a href=\"wiki.php?action=article&name=invite\">邀请</a> 他们。",
    'short_14' => "不要让你的账号处于非活动状态。",
    'long_14' => "为保证账号处于良好状况，你应定期登录 ${site_name}。如果做不到，你的账号会被禁用，详情请查看 <a href=\"wiki.php?action=article&id=73\">不活跃账号</a> 一文。",
    'short_21' => "不要邀请劣质用户。",
    'long_21' => "你需要对你邀请的用户负责。你邀请的用户达不到合格分享率，不会导致你受罚。但若你邀请的用户违反了黄金规则，你的账号和／或相关权限可能会被禁用。",
    'short_22' => "禁止交易、出售、公开赠送或公开提供邀请。",
    'long_22' => "只邀请你认识和信任的人，若非如此，请不要邀请他们。邀请主要是指你现实中认识的朋友，若邀请互联网朋友，你应该认识并相信他们。不要在未设置等级限制的 PT 站内论坛板块、贴吧、论坛、聊天软件群（QQ、TG、微信等，私聊除外）、社交媒体或任何公共场所提供和回应求邀请求。例外情况：管理组指定的专员可以在被批准的场所提供邀请。注意不要让发邀帖被搜索引擎抓到。",
    'short_23' => "禁止随意请求邀请或者账号。",
    'long_23' => "在升级到 Power User 后可访问 ${site_name} 的邀请相关板块。有些站点只允许发布官方邀请，禁止在其他站点论坛发邀和求邀，在求邀前必须先行阅读 <a href=\"forums.php?action=viewthread&threadid=324\">求邀区版规</a> 和 <a href=\"forums.php?action=viewthread&threadid=331\">禁止求邀列表</a> 。如果其他用户不曾表示可以提供邀请，则你不能以任何方式向其求邀，包括私信。",
    'short_24' => "禁止公开泄露站点信息",
    'long_24' => "不要在任何公共区域泄露站点真实名称和简称（可用海豚、海豚音乐替代）、服务器地址以及 Tracker 地址，截图时请遮蔽站点 Logo。<i style=\"color: #ed5454\">Update! 2019-07-11</i>",
    'short_31' => "禁止参与分享率作弊。",
    'long_31' => "通过使用 BitTorrent 协议或站点功能的漏洞（例如滥用 <a href=\"rules.php?p=requests\">求种</a>）来伪造发布／下载量或修改分享率数据是被绝对禁止的。如有疑问，请 ${staffpm} 以了解详情。",
    'short_32' => "禁止向 Tracker 上报非正常统计数据（即禁止作弊）。",
    'long_32' => "禁止向 Tracker 报告错误数据，不管是使用 “能作弊的客户端” 还是利用受批准的客户端上报虚假数据。",
    'short_33' => "禁止使用未获批准的客户端。",
    'long_33' => "本站 Tracker 采用 <a href=\"rules.php?p=clients\">白名单</a> 模式，仅允许使用获批的客户端，魔改版是不被允许的，测试版或 CVS 版请私信管理员。",
    'short_34' => "禁止修改 ${site_name} 的种子文件。",
    'long_34' => "将非 ${site_name} 的 Tracker URL 加入到 ${site_name} 种子的行为是被禁止的。这样做会产生错误数据并被视作作弊。此规则同时适用于未在客户端运行的种子和已在客户端运行的种子。",
    'short_35' => "禁止将种子文件或密钥分享给他人。",
    'long_35' => "每个 ${site_name} 的种子文件中都嵌入了包含你个人密钥的 URL，密钥使用户能够向 Tracker 报告数据，密钥泄露将有可能严重破坏你的分享率。",
    'short_41' => "禁止威胁、曝光、敲诈用户及管理员。",
    'long_41' => "禁止以任何理由暴露或威胁用户的隐私信息。隐私信息包括但不限于个人识别信息（例如姓名、记录、活动日志细节、照片）。未经许可，不得讨论或共享未经用户自愿公开提供的信息。包括通过搜集已公开信息（如谷歌搜索结果）来获取私人信息。",
    'short_42' => "禁止欺诈。",
    'long_42' => "禁止任何形式的诈骗（如网络钓鱼）。",
    'short_43' => "尊重管理组的决定。",
    'long_43' => "只允许与 Moderator 私下讨论分歧。如果 Moderator 已退休或联系不上，你可以 ${staffpm}。不允许因个人理由联系多位 Moderator；但是，如果你需要第三方意见，可以联系 Administrator。联系管理员的方式包括私信、${staffpm} 和交流群。",
    'short_44' => "禁止冒充工作人员。",
    'long_44' => "禁止在站内、站外或交流群冒充管理员或官方服务账号。也禁止歪曲管理员的决定。",
    'short_45' => "禁止网络霸凌。",
    'long_45' => " “网络霸凌” 是指对其他用户的指手画脚的行为。禁止对立、挑衅或攻击涉嫌违反规则的用户及被举报的用户。如果你发现违规行为，举报就够了。",
    'short_46' => "不要要求优惠活动。",
    'long_46' => "优惠活动（如种子免费、候选通过等）由管理员自行决定。它们不遵循固定的安排，用户不得提出类似要求。",
    'short_47' => "禁止收集用户识别信息。",
    'long_47' => "禁止使用 ${site_name} 的服务通过脚本、漏洞或其他技术来获取任何类型的用户识别信息（如 IP 地址、个人链接等）。",
    'short_48' => "禁止利用 ${site_name} 的服务（包括 Tracker、网站和交流群）来牟取商业利益。",
    'long_48' => "禁止将 ${site_name} 提供的服务（例如 Logchecker、Gazelle、Ocelot）及维护的代码商业化。通过上述服务（如用户种子数据等）商业化 ${site_name} 用户提供的资源是被禁止的。其他推广、募捐及交易行为也被禁止。",
    'short_51' => "禁止使用免费的代理或 VPN 浏览 ${site_name}。",
    'long_51' => "禁止通过公用或免费的代理、VPN、Tor 浏览网站，你可以通过付费 VPN、私有服务器（盒子）和代理来浏览网站。另外，不允许通过 Tor 网络连接 Tracker 服务器（下载）。如有疑问请使用 ${staffpm}。<i style=\"color: #ed5454\">Update! 2020-06-04</i>",
    'short_52' => "禁止滥用自动访问网站。",
    'long_52' => "所有自动化站点访问都必须通过指定的 <a href=\"https://github.com/WhatCD/Gazelle/wiki/JSON-API-Documentation\">API</a> 完成。API 在 10 秒内只会回应 5 个请求。 脚本和其他自动化流程不得收集网站的 HTML 页面。如有疑问，请咨询管理员。",
    'short_53' => "禁止自动收集免费种子。",
    'long_53' => "禁止使用基本不需要真人操作的方式（例如，基于 API 的脚本、日志或站点抓取等）自动收集免费种子。详情请参阅 ${site_name} 的 <a href=\"wiki.php?action=article&id=94\">免费种子自动收集政策</a> 一文。",
    'short_61' => "禁止寻找或利用现有的 BUG。",
    'long_61' => "禁止在站点中实时寻找或利用 BUG（你可以在本地开发环境试验）。如果你发现了严重错误或安全漏洞，请立即按照 ${site_name} 的 <a href=\"wiki.php?action=article&id=95\">漏洞报告政策</a> 进行报告。也可以在 <a href=\"forums.php?action=viewforum&forumid=3\">论坛的反馈版块</a> 报告不太严重的 BUG。",
    'short_62' => "禁止公布漏洞。",
    'long_62' => "有关漏洞的发布、组织、传播、分享、技术讨论或研究便利权利由管理组决定。漏洞被定义为对内部、外部、非营利或营利性服务的意料之外或未被许可的利用。详情请参阅 ${site_name} 的 <a href=\"wiki.php?action=article&id=95\">漏洞报告政策</a> 一文。漏洞利用可随时重新分类。",
    'short_70' => "尊重所有管理组成员。",
    'long_70' => "${site_name} 的工作人员是志愿者，他们将私人时间用于维护网站运行，而这是没有任何补偿的。不尊重他们可能会导致被警告甚至更严重的后果。",
    'short_71' => "管理组对规则拥有最终解释权。",
    'long_71' => " ${site_name} 的所有规则可能会有不同的解释。鉴于管理组编写了这些规则，他们拥有最终解释权。如果你对本文感到疑惑或不解，或者你认为应该重新制定规则，请发送 ${staffpm}。",

    'ratio_title' => "分享率",
    'ratio_used' => "概述",
    'ratio_summary_a' => "你的<strong>分享率</strong>等于你发布量除以下载量的商。你可以在站点页面的最上方或者你个人信息的 “统计” 部分看到。",
    'ratio_summary_b' => "为了能够享有<strong>下载种子的权限</strong>，你的分享率必须保持在某一最小值之上。这个最小值就是你的<strong>合格分享率</strong>。",
    'ratio_summary_c' => "如果你的分享率低于你的合格分享率，你将会有两周的时间来提高你的分享率并使之高于你的合格分享率。在这期间，你将被列入<strong>分享率监控名单</strong>。",
    'ratio_summary_d' => "如果在给定时间内，你没有将分享率提高到合格分享率以上，你将失去下载权限，即无法下载更多的资源。但你仍然可以进行除了下载以外的正常活动。",
    'ratio_used_a' => "合格分享率概述",
    'ratio_summary_a_a' => "合格分享率就是你必须维持的最低分享率，否则你会被列入分享率监控名单。你可以在站点最上方的 “合格分享率” 后边看到，或者在个人信息的 “统计” 部分看到。",
    'ratio_summary_b_b' => "合格分享率因人而异。每个用户的合格分享率是由其账号流量数据计算得来的。",
    'ratio_summary_c_c' => "你的合格分享率是根据以下两点计算得来的：（1）你的总下载量；（2）你当前的总做种数。总做种数不仅包含你已完成的种子（完成下载），也包含但不限于你已发布的种子。",
    'ratio_summary_d_d' => "随着你做种数量的增加，系统会将合格分享率降低。你的做种数量越多，你需要达到的合格分享率就越低，进而你就不容易被列入分享率监控名单。",
    'ratio_table' => "分享率规则",
    'ratio_dl' => "用户下载量",
    'ratio_dl_title' => "这些单位是二进制而非十进制的，举个例子，1 GB 中有 1024 MB。",
    'ratio_re_0' => "合格分享率（0% 做种）",
    'ratio_re_100' => "合格分享率（100% 做种）",
    'ratio_sum' => "合格分享率计算：",
    'ratio_1' => "<strong>1. 计算合格分享率可能的最大值和最小值</strong>。使用上述表格，在第一列中查看你账号下载资源所在的范围。接下来，查看相邻一列的数值。第二列给出了每个下载资源量对应的最大合格分享率，此时对应 0% 做种的情形。第三列给出了每个下载资源量对应的最小合格分享率，对应 100% 做种的情形。",
    'ratio_2' => "<strong>2. 计算实际的合格分享率</strong>。你的实际合格分享率数值将处于最大值和最小值之间。为了计算你的实际合格分享率，系统会首先将最大合格分享率与数值 [1-(做种数/完成数)] 相乘。更直观的表达式如下所示：",
    'ratio_show' => "<li>说明：在上述公式中，<var>完成数</var>表示你已下载完成的且未被系统删除的种子数量。如果同一个种子下载两次，公式中只会计算一次。如果下载完成的种子被站点删除了，它就不会被计算在上式之内。
		</li>
					<li>在上述公式中，<var>做种数</var>是你过去一周做种时间超过 72 小时的平均做种数量。如果一个种子在过去的一周内做种时间不足 72 小时，它将不会计入你的做种数量。请注意，尽管做种数有可能大于完成数，系统会将你的做种比限制在 100%。
		</li>",

    'ratio_3' => "<strong>3. 如有必要，在上述步骤中得到的值会四舍五入到你的最低合格分享率中</strong>。这是因为，当做种数等于完成数时，上述公式计算返回的值为 0，但对于大多数账号而言，最低合格分享率要大于 0。",
    'ratio_summary_1' => "合格分享率详解：",
    'ratio_summary_1_con' => "<li>如果你超过一周没有做种，你的合格分享率就会变为最高分享率。一旦你重新开始做种并持续 72 小时，根据上述公式，你的合格分享率就会降低。
</li>
			<li>如果下载量低于 5 GB，你不会被列入分享率监控名单且不会被要求达到某个合格分享率。在此情况下，无论做种比例如何，你的合格分享率都是 0。
</li>
			<li>如果你的下载量低于 20 GB 且做种数等于完成数，你的合格分享率将会为 0。
            </li>
			<li>随着你下载量的增加，你的最小和最大合格分享率会逐渐接近。当你的下载量达到 100GB 时，这两个数值会相等。即当用户下载量大于等于 100GB 后，其最小合格分享率将会恒为 0.6。
</li>",

    'ratio_summary_2' => "合格分享率举例：",
    'ratio_summary_2_con' => "<li>比如，张三下载了 25 GB 资源，通过查询上述表格得知，该数值位于 20~30 GB 范围之间。张三的最大合格分享率是 0.30，最小合格分享率是 0.05。
</li>
			<li>而张三下载完成了 90 个种子，当前正在做种的有 45 个种子。为了计算张三的实际合格分享率，我们将他的最大合格分享率 0.3 乘以[1-(做种数/完成数)]，即:
				<samp>0.30 × [1 &minus; (45 / 90)] = 0.15</samp>
</li>
			<li>计算得出的合格分享率为 0.15，处于最大值 0.30 和最小值 0.05 之间。</li>
			<li>若网站所显示的张三的合格分享率大于上述计算值，那是由于在过去一周内，他所做种的 45 个种子尚未达到 72 小时。在这种情况下，系统不会将做种数认定为 45。
</li>",

    'ratio_summary_3' => "分享率监控总则：",
    'ratio_summary_3_con' => "<li>在分享率监控启动之前，每个用户都可以下载 5GB 资源。</li>
			<li>如果你已经下载了 5 GB 以上资源且你的分享率未达到合格分享率，你将会被列入分享率监控名单，你将有<strong>两周</strong>时间来改善你的分享率，使之高于合格分享率。
</li>
			<li>当你处于分享率监控名单时又下载了 10GB 资源，你的下载权限将会被自动禁用。</li>
			<li>如果在两周之内你无法脱离分享率监控名单，你将会失去下载权限。此后，你将无法下载更多资源。你的账号仍然可以登录。
</li>
			<li>分享率监控系统是自动运行的，无法被管理员手动干预。</li>",

    'ratio_summary_4' => "脱离分享率监控名单：",
    'ratio_summary_4_con' => "<li>为了脱离分享率监控名单，你必须通过发布更多的资源来提高你的分享率，或者通过提高做种数来降低你的合格分享率。你的分享率必须大于等于合格分享率以脱离分享率监控名单。</li>
			<li>如果在分享率监控期限结束时，你未能提高你的分享率，你将会失去下载权限，你的合格分享率会被临时设定为可能的最高分享率（如同你 0% 做种的情形）。
</li>
			<li>失去下载权限后，为了调整合格分享率，以便正确反映正在做种的数量，你必须在一周时间内做种 72 小时以上。当达到 72 小时后，合格分享率就会更新，并反映你当前的做种数量，与有下载权限的用户一样。</li>
			<li>一旦你的分享率大于等于合格分享率，下载权限就会被恢复。</li>",




    'requests_title' => "求种",
    'requests_summary' => "<li><strong>不要求违规种子。</strong>遵守求种规则是你的责任与义务。若不守规矩，你的求种会被删，且已支付的发布量不会退还给你。求种不可以比发布（替代）规则更具体。比如，规则中已经禁止以无 Log 的 MP3 资源去替换旧有资源，你若要求一份带 Log 的 MP3，这样的求种是在请求重复发布。
</li>
			<li><strong>一个求种一份资源（专辑、应用软件等）。</strong>在一个求种中提请多个专辑（例如一类专辑）抑或是含糊不清的求种都是不被允许的。你可能想要多种格式，但你不能全都要。举个例子，你可能需要 FLAC 和 V0 两种，但你不能同时选择它们。你也可能提出了某个艺术家的多张专辑的请求，但这个请求可以被该艺术家的一张专辑应求。应用软件类请求仅能包含一个应用软件，但可以给出允许的版本跨度。然而，这样的请求可以被该范围内的某个版本应求。
</li>
			<li><strong>不要因过分挑剔而否决应求。</strong>如果你没有在求种中明确提出你的精细要求（比如比特率或特定版本），你就不能否决应求并随后更改求种描述。不要因为你的无知否决应求（比如应求的种子可能是转码后的资源但你搞不清楚）。在此种情况下，你可以向一线支持求助。当应求种子确实没有满足你已经阐释清除的要求时，你可以否决该应求。
</li>
			<li><strong>应求面前，人人平等。</strong>发布量交易是不被允许的。通过滥用求种系统来为其他用户牟取便利是不可饶恕的，包括为特定用户量身定制求种（无论是否在求种中写明）。我们严厉禁止模糊化求种要求，而后否决其他人的应求以使某个特定用户能够应求。如被举报，无论是求种者还是被 “钦定” 的应求者都会被警告并扣除该求种相应的发布量。
</li>
			<li><strong>禁止要求求种者上调求种报酬。</strong>发布量报酬是对助人为乐的奖赏——而不是赎买。任何求种者不加价就不应求的用户将会面临严厉的惩罚。
</li>",


    'collages_title' => "合集",
    'collages_summary' => "<li> “作品名录” “管理精选” “唱片厂牌” 和 “音乐榜单” 合集必须基于事实而非个人观点。如果某内容是已出版的 “Best of xxx” （譬如 “Pitchfork's Best Albums of the 1990's” ，它应被归入 “音乐榜单” 类别。</li>
			<li> “私人合集” “主题合集” 和 “类型介绍” 合集可以基于个人观点。在创建和提供 “主题合集” 和 “类型介绍” 合集时，你必须尊重他人的意见。</li>
			<li>破坏合集的行为会被严惩，导致编辑合集的权限被剥夺（最轻处罚）。</li>
			<li>私人最爱（Personal Best Of）一类的合集仅允许放入 “私人合集” 类别中。你必须达到 Power User 及以上或者捐助才能创建 “私人合集”。</li>
			<li>特定人群，比如 Torrent Master 或面试官，在获得管理员授权之后，可以创建一个群体精选 “主题合集” （Group Picks Theme collage）。</li>
			<li>
			每种 “类型介绍”／“主题合集” 只能拥有一个合集，重复的合集会被删除。</li>
			<li> “类型介绍”／“主题合集” 合集对于大众而言必须是合情合理的，否则会被删除。</li>
			<li>合集不是标签系统的替代品。譬如，一个名为 “mathcore torrents” 的合集是不被允许的，因为仅仅把给它们添上 mathcore 标签更合适。当然了， “xysite.com 最糟糕的 50 个 mathcore 专辑” 另当别论。</li>
			<li>合集不允许被用来创建艺术家作品目录，因为艺术家页面存在的目的就是这个。但是，对于拥有众多编外项目的艺术家，创建一个包含所有这些编外项目的合集并放入 “作品名录” 这一类别是被允许的。</li>
			<li>Power User 及捐助者可以创建 1 个 “私人合集”。Elite User 可以创建 2 个，Torrent Master 可以创建 3 个，Power TM 可以创建 4 个，Elite TM+ 可以创建 5 个。捐助者可以在其等级基础上再增加 1 个 “私人合集”。</li>
			<li>每个合集当中应至少拥有 3 个种子组， “唱片厂牌” “私人合集” “管理精选” 合集除外。</li>
			<li>请先检查确认类似的合集尚不存在。如果类似合集已存在，请为其添砖加瓦。</li>
			<li>请为你的合集起个恰当的名字并阐释其创建目的。</li>
			<li>请尽量为你合集中的每一个种子添加专辑封面。</li>",


    'clients_title' => "客户端",
    'clients_list' => "客户端白名单",
    'clients_summary' => "客户端规则是维持我们群体正直诚实的保障。它保证了我们能够将具有破坏性和欺骗性的客户端（比如迅雷）拒之门外，因为这些东西会破坏我们 Tracker 的正常运行、损害我们用户的利益。</br></br>
    <strong>使用 <a href='https://github.com/c0re100/qBittorrent-Enhanced-Edition/releases'>修改版客户端</a> 可能会导致数据统计错误，使用它会导致你被警告，乃至禁用账号，请务必使用官方三位数版本号的客户端。</strong>",


    'upload_title' => "发布规则",
    'upload_search' => "输入关键词",
    'upload_search_note' => "示例：搜索 <strong>MKV</strong> 得到与 <strong>MKV</strong> 相关的规则。搜索词 <strong>MKV</strong> + <strong>trump</strong> 得到所有与 <strong>MKV</strong> 和 <strong>trump</strong> 相关的的规则。",

    'upload_h1k' => "发布什么",
    'upload_h11k' => "允许内容",
    'upload_h12k' => "特别禁止",
    'upload_h13k' => "Scene 发布",
    'upload_h13k_a' => "<a href='wiki.php?action=article&amp;id=140'>Scene</a> 发布",

    'upload_h2k' => "必需信息",
    'upload_h21k' => "命名",
    'upload_h22k_t' => "截图",
    'upload_h22k' => "截图：在发布页面的 “种子描述” 中，仅要求至少三张与影片分辨率相同的 PNG 格式的截图。",
    'upload_r220' => "总览",
    'upload_r220_note' => "这张图表是重复和替代规则的总览。",
    'upload_r229k' => "有损规则",
    'upload_r2210k' => "无损规则",
    'upload_r2211k' => "版本与发行规则",
    'upload_h23k_t' => "Mediainfo",
    'upload_h23k' => "Mediainfo：你发布内容的规格必须使用 MediaInfo 或用于 Blu-ray 的 BDInfo 提供。如果一个种子包含了多个视频文件，则应为每个文件都提供视频 Encode 信息。",
    'upload_h24k_t' => "电影海报",
    'upload_h24k' => "电影海报：你必须为你的电影提供一张封面图（例如电影海报、VHS 或 DVD 封面）。尽你所能搜索封面，但若是一无所得，则包含片名的一张截图也可。",
    'upload_h25k_t' => "其他发行信息",
    'upload_h25k' => "其他发行信息：任何你在发布页面填写的内容应与资源本身相符。",
    'upload_h26k' => "现场音乐和调音台",
    'upload_r2691k' => "允许的现场音乐",
    'upload_r2692k' => "不允许的现场音乐",
    'upload_h27k' => "多声道",
    'upload_h28k' => "SACD",
    'upload_h29k' => "蓝光",
    'upload_h210k' => "磁带",

    'upload_h3k' => "格式说明",
    'upload_h31k' => "标清（SD）",
    'upload_h32k' => "高清（HD）",
    'upload_h33k' => "超高清（UHD）",
    'upload_h34k' => "原盘",
    'upload_h35k' => "附加内容",

    'upload_h4k' => "共存",
    'upload_h41k' => "标清",
    'upload_h42k' => "高清",
    'upload_h43k' => "超高清",
    'upload_h44k' => "原盘",
    'upload_h45k' => "附加内容",
    'upload_h46k' => "其他",

    'upload_h5k' => "替代",
    'upload_h51k' => "源",
    'upload_h52k' => "质量",
    'upload_h53k' => "不活跃",
    'upload_h54k_t' => "可替代标记",
    'upload_h54k' => "可替代标记：这些标记会附加在任何未达到我们标准的种子上。",

    'upload_h6k' => "额外信息",
    'upload_h61k' => "不要发布你没有完全访问权限的种子或内容。无论是在本地还是在盒子，你都必须在制作种子并发布之前拥有内容的完全处置权。",
    'upload_h62k' => "不要发布你不打算做种的种子。本站要求你为所有的种子在两周内做种至少 48 小时，或直至你的分享率达到 1，即输出了一个完整的副本。即使你是种子的发布者，此规则也同样适用。参见 <a href='rules.php?p=ratio'>HnR 规则</a> 了解更多。",
    'upload_h63k' => "尽你所能地长期做种。本站旨在成为所有电影、所有规格、永久的档案馆，你做种越久，我们离梦想就越近，你的分享率也越好看。尽量不要让做种达到底线值成为你的习惯，你应对自己有所要求。",
    'upload_h64k' => "在站点活动时考虑自身状况，每个种子都应该是可供下载的，即使慢一些。如果你的网络连接速度很满，请考虑慢慢发种以保证新的下载者能连得上。不要故意将带宽限制到无法使用的速度。",

    'upload_introk' => "介绍",
    'upload_introk_note' => "<p>为保证资源质量，下面的发布规则繁多且详细。为清楚和彻底地解释规则，我们认为这个长度是必要的。每条规则的摘要在其详细说明之前以<span style='font-weight: bold;'>粗体</span>显示，以便于阅读。你还可以在索引中找到相应的规则部分。序号前的 “↑” （返回至 <a href='#Index'>目录</a>）和 <a href='#Index'>规则链接</a>（跳转至详细说明）可助你快速导航。</p>
    <p>在发布任何内容之前，如果你仍然不确定规则的含义，请在站内寻求支持：<a href='staff.php'>一线支持</a>、<a href='forums.php?action=viewforum&amp;forumid=16'>论坛咨询</a> 或在 <a href='wiki.php?action=article&amp;name=IRC'><?= BOT_HELP_CHAN ?>IRC</a> 上提问。如果其他帮助人员已将你引导至管理员或在你遇到的情况下无济于事，请 <a href='staffpm.php'>私信管理</a>。如果你在发布规则中发现任何失效的链接，请 <a href='staffpm.php'>私信管理</a>，并在你的信息中包含发布规则编号（例如 <a href='#r2.4.3'>2.4.3</a>），最好是带有正确的链接以替代失效的链接。</p>",
    'upload_h11k_note' => "<li id='r1.1.1'><a href='#h1.1'><strong>&uarr;_</strong></a> <a href='#r1.1.1'>1.1.1.</a>
                 <strong>长片：</strong>长片指的是任意时长大于 45 分钟的电影。如果某部电影于短片而言太长，于长片而言又太短，请查询 IMDb。
                </li>
                <li id='r1.1.2'><a href='#h1.1'><strong>&uarr;_</strong></a> <a href='#r1.1.2'>1.1.2.</a>
                    <strong>短片：</strong>简而言之就是短于长片的电影。其时长范围从数秒到大约 45 分钟不等。
                </li>
                <li id='r1.1.3'><a href='#h1.1'><strong>&uarr;_</strong></a> <a href='#r1.1.3'>1.1.3.</a>
                <strong>单口喜剧：</strong>单口相声演员的电影形式表演。无论时长长短，都归属于此类。发布非演员官方发行的任何表演都属于违规行为。
                </li>
                <li id='r1.1.4'><a href='#h1.1'><strong>&uarr;_</strong></a> <a href='#r1.1.4'>1.1.4.</a>
                    <strong>迷你剧集：</strong>迷你剧集是一类长期的、在电视上单集播放的剧情片或纪录片。它不是电视连续剧，因为它在计划播完的剧集之后并无续集或下一季。
                </li>
                <li id='r1.1.5'><a href='#h1.1'><strong>&uarr;_</strong></a> <a href='#r1.1.5'>1.1.5.</a> <strong>独立单元剧：</strong>独立单元剧指的是为电视制作的剧集之外的剧集。每一集都必须是独立的故事片或短片。独立单元剧中的每一集都必须单独发布。集间故事情节有关联，但布景和／或演员每季都有变更的 “独立单元剧系列” 是不允许的。
                </li>
                <li id='r1.1.6'><a href='#h1.1'><strong>&uarr;_</strong></a> <a href='#r1.1.6'>1.1.6.</a>
                    <strong>纪录片系列剧：</strong>若与 <a href='#r1.1.4'>1.1.4</a> 相似，即每一季拥有一个总的主题（如 BBC 的《蓝色星球》第一二季）；或与 <a href='#r1.1.5'>1.1.5</a> 相似，即每一集的情节相互独立（如 ESPN 的《30 for 30》），则纪录片系列剧也允许发布。
                </li>
                <li id='r1.1.7'><a href='#h1.1'><strong>&uarr;_</strong></a> <a href='#r1.1.7'>1.1.7.</a>
                    <strong>现场表演：</strong>任何官方发行的音乐会、演艺、剧院演出录像，或任何介于它们之间的内容。官方发行的盗版和音乐会流是不允许的。
                </li>
                <li id='r1.1.8'><a href='#h1.1'><strong>&uarr;_</strong></a> <a href='#r1.1.8'>1.1.8.</a>
                    <strong>电影集：</strong>当且仅当套盒或合辑中的多部电影共用光盘且无法分离时，才允许原封不动发布。这也包括单张光盘上的多部电影。Encode 作品则必须分开发布。
                </li>",
    'upload_h12k_note' => "
                    <li id='r1.2.1'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.1'>1.2.1.</a>
                    <strong>预售：</strong>任何预售（包括但不限于 CAM、TS、TC、R5、DVDScr）都是不允许的。
                </li>
                <li id='r1.2.2'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.2'>1.2.2.</a>
                    <strong>电视节目：</strong>禁止电视节目或电视连续剧。这不包括为电视制作的电影或规则 <a href='#r1.1.4'>1.1.4</a>、<a href='#r1.1.5'>1.1.5</a> 和 <a href='#r1.1.6'>1.1.6</a> 中定义的管理批准的迷你剧集和独立单元剧。
                </li>
                <li id='r1.2.3'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.3'>1.2.3.</a>
                    <strong>色情：</strong>本站不允许任何被 IMDb 添加成人标签的爱情动作片或电影。如果你觉得一部电影被打上成人标签并不公正，请在发布前申请管理的批准。
                </li>
                <li id='r1.2.4'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.4'>1.2.4.</a>
                    <strong>MV 集锦：</strong>它们不是完整长度的音乐会、纪录片或短片。
                </li>
                <li id='r1.2.5'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.5'>1.2.5.</a>
                    <strong>体育视频：</strong>禁止棒球比赛、摔跤比赛、汽车比赛、极限运动剪辑等。有关体育的纪录片是允许的。如果你不太清楚其中的区别，请在发布前申请管理的批准。
                </li>
                <li id='r1.2.6'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.6'>1.2.6.</a>
                    <strong>影迷剪辑：</strong>只允许官方发行的内容或电影。
                </li>
                <li id='r1.2.7'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.7'>1.2.7.</a>
                    <strong>视频教程：</strong>任何类型的教学和培训视频都是不允许的。电影制作相关的内容须经管理批准。
                </li>
                <li id='r1.2.8'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.8'>1.2.8.</a>
                    <strong>非视频种子：</strong>在任何情况下，你的种子都不应包含非视频文件。这包括压缩存档格式（RAR、ZIP……）。
                </li>
                <li id='r1.2.9'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.9'>1.2.9.</a>
                    <strong>打包电影：</strong>一个种子一部电影。包含多部电影的套盒只能原封不动地发布。有关发布套盒的更多信息，参见规则 <a href='#r1.1.4'>1.1.8</a>。
                </li>
                <li id='r1.2.10'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.10'>1.2.10.</a>
                    <strong>电影与附加内容合在一起：</strong>附加内容必须与电影本体分开发布，除非原封不动地发布源文件。
                </li>
                <li id='r1.2.11'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.11'>1.2.11.</a>
                    <strong>不允许有损转码：</strong>所有 Rip 使用的源都必须是电影完整的原装的形式（这包括 BDRip 和重新压缩的 DVD5）。
                </li>
                <li id='r1.2.12'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.12'>1.2.12.</a>
                    <strong>低质量发行：</strong>目前的名单：aXXo、BRrip、CM8、CrEwSaDe、DNL、EVO (WEB-DL 允许)、FaNGDiNG0、FRDS、HD2DVD、HDTime、iPlanet、KiNGDOM、Leffe、mHD、mSD、nHD、nikt0、nSD、NhaNc3、PRODJi、RDN、SANTi、 STUTTERSHIT、TERMiNAL (低比特率 UHD)、ViSION、WAF、x0r、YIFY、PSP/iPad/移动设备预设 Encode 。
                </li>
                <li id='r1.2.13'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.13'>1.2.13.</a>
                    <strong>预告片集锦：</strong>不允许预告片集锦。
                </li>
                <li id='r1.2.14'><a href='#h1.2'><strong>&uarr;_</strong></a> <a href='#r1.2.14'>1.2.14.</a>
                    <strong>特别禁止内容：</strong>不允许发布任何罗列在我们 <a href='torrents.php?action=do_not_upload_movie_list'>黑名单</a> 上的内容。
                </li>",
    'upload_h13k_note' => "
                        <li id='r1.3.1'><a href='#h1.3'><strong>&uarr;_</strong></a> <a href='#r1.3.1'>1.3.1.</a>
                    <strong>你可以向发布小组致谢（可选）。</strong>如果你想要添加这部分信息，请在种子描述中填写完整的、包含小组名的原始发布标题。请不要将这些信息填入专辑描述。
                </li>
                <li id='r1.3.2'><a href='#h1.3'><strong>&uarr;_</strong></a> <a href='#r1.3.2'>1.3.2.</a>
                    <strong>请不要把 .nfo 文件中的内容粘贴在专辑描述。</strong>没有改动过的 .nfo 文件内容是可以填写在种子描述部分的，但不是专辑描述。如果你必须添加 .nfo 文件中的部分内容，请注意，专辑描述只允许出现曲目列表、专辑介绍等必要的信息。其他如编码信息等请填入种子描述部分内。
                </li>
                <li id='r1.3.3'><a href='#h1.3'><strong>&uarr;_</strong></a> <a href='#r1.3.3'>1.3.3.</a>
                    <strong>请在 <a href='upload.php'>发布页面</a> 填写正确的专辑标题，不要填入发布小组给的标题。</strong>专辑标题中请不要将 “_” 或 “.” 作为连接符。请填写实际的发行标题和艺术家名。不要使用 scene 发行的种子文件夹或 NFO 内提供的标题。
                </li>
                <li id='r1.3.4'><a href='#h1.3'><strong>&uarr;_</strong></a> <a href='#r1.3.4'>1.3.4.</a>
                    <strong>如果勾选 scene 选框，请确保 scene 音乐文件和发布时一样。</strong>如果你改动了元数据标签或解压了压缩文件、移除某些文件、划分了音轨、更改了音轨标题等，该文件即不算 scene 音乐，且不可再勾选 scene 选框。如果进行了以上改动，在文件名中即不可再含有 scene 组的信息。更多信息请参见 <a href='#r1.1.5'>1.1.5</a>、<a href='#r2.3.2'>2.3.2</a> 和 <a href='#r2.3.11'>2.3.11</a>。
                </li>
                <li id='r1.3.5'><a href='#h1.3'><strong>&uarr;_</strong></a> <a href='#r1.3.5'>1.3.5.</a>
                    <strong>禁止含解压密码的压缩文件。</strong>压缩文件必须无密码发布。
                </li>
                <li id='r1.3.6'><a href='#h1.3'><strong>&uarr;_</strong></a> <a href='#r1.3.6'>1.3.6.</a>
                    <strong>每个分类下的 scene 种子都必须符合对应板块的规则。</strong>例如，scene 音乐种子必须满足音乐质量和格式的要求，不管 scene 发布时的具体情况如何。如果 scene 压缩文件是有密码保护的，在不改动的情况下，该文件即不可发布。例外：原文件不符合规则，但经过了必要的一点修改之后，该文件可以符合规则，此时，即可以发布。但是必须要注意，改动之后，则不能勾选 scene 选框。
                </li>",
    'upload_h21k_note' => "<ul>

                <li id='r2.1.1'><a href='#h2.1'><strong>&uarr;_</strong></a> <a href='#r2.1.1'>2.1.1.</a> <strong>文件名和／或文件夹名必须与电影的原始语言标题或 IMDb 中提供的国际英文标题之一相匹配。</strong>
                    <ul>
                    <li id='r2.1.1.1'><a href='#r2.1.1'><strong>&uarr;_</strong></a> <a href='#r2.1.1.1'>2.1.1.1.</a> Internal Remux 的文件名需要在影片标题后包含（格式或顺序不限）原始发行年、分辨率，以及音视频编码（例如 The.Thing.1982.1080p.AVC.DTS-HD.MA，或 Citizen Kane (1941) 1080p H264 FLAC）。</li>
                    </ul>
                </li>
                <li id='r2.1.2'><a href='#h2.1'><strong>&uarr;_</strong></a> <a href='#r2.1.2'>2.1.2.</a> <strong>压制组发行（来自 P2P 组或 Scene 组）不应重命名，</strong>除非它们不满足规则 <a href='#r2.1.1'>2.1.1</a> 或我们的文件名要求。
                </li>
                <li id='r2.1.3'><a href='#h2.1'><strong>&uarr;_</strong></a> <a href='#r2.1.3'>2.1.3.</a> <strong>保持文件夹内文件尽可能简洁。</strong>不要包含：样片、截图、Desktop.ini/thumbs.db 文件或其他任何与你要发布的内容不完全相关的东西。与翻录过程相关的文件允许放在 DVD/BD 的目录结构中。
                </li>
                <li id='r2.1.4'><a href='#h2.1'><strong>&uarr;_</strong></a> <a href='#r2.1.4'>2.1.4.</a> <strong>DVD 和 BD 文件目录结构不允许改动，仅顶层文件夹允许重命名。</strong>
                </li>
            </ul>",
    'upload_h22k_note' => "<ul>
                <li id='r2.2.1'><a href='#h2.2'><strong>&uarr;_</strong></a> <a href='#r2.2.1'>2.2.1.</a> <strong>截图应存放在 <a href='https://ptpimg.me'>https://ptpimg.me</a>。</strong>作为替代品，使用 <a href='https://pixhost.to'>https://pixhost.to</a> 或 <a href='https://malzo.com'>https://malzo.com</a> 图床的外链也是允许的。
                </li>
            </ul>",
    'upload_h23k_note' => "
            <ul>
                <li id='r2.3.1'><a href='#h2.3'><strong>&uarr;_</strong></a> <a href='#r2.3.1'>2.3.1.</a> <strong>不得编辑 MediaInfo 日志。</strong>如果你确定它不对，请通过报告提交必要的修正。
                </li>
            </ul>",
    'upload_h24k_note' => "
            <ul>
                <li id='r2.4.1'><a href='#h2.4'><strong>&uarr;_</strong></a> <a href='#r2.4.1'>2.4.1.</a> <strong>能获取到官方艺术作品时就不允许影迷的作品。</strong>
                </li>
                <li id='r2.4.2'><a href='#h2.4'><strong>&uarr;_</strong></a> <a href='#r2.4.2'>2.4.2.</a> <strong>影院海报相对而言是首选，且不允许实体碟的照片。</strong>
                </li>
                <li id='r2.4.3'><a href='#h2.4'><strong>&uarr;_</strong></a> <a href='#r2.4.3'>2.4.3.</a> <strong>对于此类图片的存放，要求同截图，见规则 <a href='#r2.2.1'>2.2.1</a>。</strong>
                </li>
            </ul>",
    'upload_h25k_note' => "
            <ul>
                <li id='r2.5.1'><a href='#h2.5'><strong>&uarr;_</strong></a> <a href='#r2.5.1'>2.5.1.</a> <strong>但凡你的资源的 IMDb 链接存在，填写它就是必须的。</strong>如果 IMDb 中对于电影缺少梗概，请考虑亲自花点时间写上。
                <ul>
                        <li id='r2.5.1.1'><a href='#r2.5.1'><strong>&uarr;_</strong></a> <a href='#r2.5.1.1'>2.5.1.1.</a> <strong>在发布音乐会时，影片描述中必须带有完整的曲目列表，IMDb 链接（如果存在）或是零售链接（比如亚马逊）也要有。</strong>
                        </li>
                    </ul>
                </li>
                <li id='r2.5.2'><a href='#h2.5'><strong>&uarr;_</strong></a> <a href='#r2.5.2'>2.5.2.</a> <strong>如果你要发布的电影版本与在影院上映的原始版本不同（导演剪辑、未分级、配音……），请勾选 “版本信息” 并挑选合适的标签。</strong>如果特殊功能有适用的标签（HDR10、Dolby Vision、Dolby Atmos、3D、2in1），也必须添加。完整的标签列表见 <a href='wiki.php?action=article&id=2'>此处</a>。
                </li>
                <li id='r2.5.3'><a href='#h2.5'><strong>&uarr;_</strong></a> <a href='#r2.5.3'>2.5.3.</a> <strong>如果你正在发布你自己的 Encode 或 Rip 作品，则应勾选 “个人 Rip”。</strong>
                </li>
                <li id='r2.5.4'><a href='#h2.5'><strong>&uarr;_</strong></a> <a href='#r2.5.4'>2.5.4.</a> <strong>对于所有的影片，字幕信息都是必填项。</strong>带有硬字幕的种子应立即打上标记或报告此情况。
                </li>
                <li id='r2.5.5'><a href='#h2.5'><strong>&uarr;_</strong></a> <a href='#r2.5.5'>2.5.5.</a> <strong>如果你发布的资源有任何可在来源站获取到的相关信息（例如源、注释、x264 日志……），则你必须将之填入影片描述。</strong>如果是你自制的影片，提供这些信息也是我们鼓励的。
                </li>
                <li id='r2.5.6'><a href='#h2.5'><strong>&uarr;_</strong></a> <a href='#r2.5.6'>2.5.6.</a> <strong>你给电影添加的标签应是客观的描述。</strong>IMDb 标签是权威的，不可靠的（主观的、有政治倾向的）标签则会被删除。标签应用于标志宽泛的类型（例如 drama 或 sci.fi），而非具体的已经有记录或更适用于合辑的东西（例如 steven.spielberg、korean、imdb.top.250）。
                </li>
                <li id='r2.5.7'><a href='#h2.5'><strong>&uarr;_</strong></a> <a href='#r2.5.7'>2.5.7.</a> <strong>请花点时间添加预告片，或是任何其他能够促使用户下载你种子的信息。</strong>对可能破坏他人对电影印象的东西保持警惕。
                </li>
            </ul>",

    'upload_h31k_note' => "
            <ul>
                <li id='r3.1.1'><a href='#h3.1'><strong>&uarr;_</strong></a> <a href='#r3.1.1'>3.1.1.</a> <strong>标清种子指的是任何未达到高清要求的种子（见规则 <a href='#r3.2.1'>3.2.1</a>）。</strong>
                <ul>
                        <li id='r3.1.1.1'><a href='#r3.1.1'><strong>&uarr;_</strong></a> <a href='#r3.1.1.1'>3.1.1.1.</a> <strong>来自标清源的 x264 Encode 在任何情况下都不允许再放大，且应根据其存储分辨率添加标签。</strong>
                        </li>
                        <li id='r3.1.1.2'><a href='#r3.1.1'><strong>&uarr;_</strong></a> <a href='#r3.1.1.2'>3.1.1.2.</a> <strong>来自高清和超高清源的 x264 Encode 必须使用 480p（最大分辨率为 854×480 像素）或 576p（最大分辨率为 1024×576 像素）分辨率。</strong>
                        </li>
                    </ul>
                </li>
                <li id='r3.1.2'><a href='#h3.1'><strong>&uarr;_</strong></a> <a href='#r3.1.2'>3.1.2.</a> <strong>SD Encode 必须使用 x264 编码和 MKV 容器。</strong>
                </li>
                <li id='r3.1.3'><a href='#h3.1'><strong>&uarr;_</strong></a> <a href='#r3.1.3'>3.1.3.</a> <strong>在电影找不到未有损转码的首选格式时，错误的编解码器、容器和分辨率可能可以容忍。</strong>如果电影能获取到正确格式，则不能再发布错误格式的，除非存在明显的质量提升。见相关的 <a href='#h4.1'>共存</a>／<a href='#h5.2'>替代</a> 规则或到 这里 询问例外情况。
                </li>
                <li id='r3.1.4'><a href='#h3.1'><strong>&uarr;_</strong></a> <a href='#r3.1.4'>3.1.4.</a> <strong>更多关于标清共存的信息，参见 <a href='#h4.1'>规则相关部分</a>。</strong>
                </li>
            </ul>",
    'upload_h32k_note' => "
            <ul>
                <li id='r3.2.1'><a href='#h3.2'><strong>&uarr;_</strong></a> <a href='#r3.2.1'>3.2.1.</a> <strong>允许的分辨率有 720p（最大分辨率为 1280×720 像素）和 1080p（最大分辨率为 1920×1080p）。</strong>
                </li>
                <li id='r3.2.2'><a href='#h3.2'><strong>&uarr;_</strong></a> <a href='#r3.2.2'>3.2.2.</a> <strong>HD Encode 必须使用 x264（存在允许 x265 的例外，详见 <a href='#r4.2.2'>4.2.2</a>）编码和 MKV 容器。</strong>
                </li>
                <li id='r3.2.3'><a href='#h3.2'><strong>&uarr;_</strong></a> <a href='#r3.2.3'>3.2.3.</a> <strong>在电影找不到未有损转码的首选格式时，错误的编解码器、容器和分辨率可能可以容忍。</strong>如果电影能获取到正确格式，则不能再发布错误格式的，除非存在明显的质量提升。见相关的 <a href='#h4.1'>共存</a>／<a href='#h5.2'>替代</a> 规则或到 这里 询问例外情况。
                </li>
                <li id='r3.2.4'><a href='#h3.2'><strong>&uarr;_</strong></a> <a href='#r3.2.4'>3.2.4.</a> <strong>高清 Encode 必须源自 Blu-ray、HD-DVD、HDTV 或 WEB。</strong>任何其他源都应 由管理批准。
                </li>
                <li id='r3.2.5'><a href='#h3.2'><strong>&uarr;_</strong></a> <a href='#r3.2.5'>3.2.5.</a> <strong>更多关于高清共存的信息，参见 <a href='#h4.2'>规则相关部分</a>。</strong>
                </li>
            </ul>",

    'upload_h33k_note' => "
            <ul>
                <li id='r3.3.1'><a href='#h3.3'><strong>&uarr;_</strong></a> <a href='#r3.3.1'>3.3.1.</a> <strong>允许的分辨率是 2160p（最大分辨率为 4096×2160 像素）。</strong>
                </li>
                <li id='r3.3.2'><a href='#h3.3'><strong>&uarr;_</strong></a> <a href='#r3.3.2'>3.3.2.</a> <strong>HDR (High Dynamic Range) 超高清源必须在编码时保持此特性。</strong>
                </li>
                <li id='r3.3.3'><a href='#h3.3'><strong>&uarr;_</strong></a> <a href='#r3.3.3'>3.3.3.</a> <strong>超高清 Encode 必须使用 x265 编码和 MKV 容器。</strong>
                <ul>
                        <li id='r3.3.3.1'><a href='#r3.3.3'><strong>&uarr;_</strong></a> <a href='#r3.3.3.1'>3.3.3.1.</a> <strong>SDR 超高清 Encode 若被规则 <a href='#r4.3.1.2'>4.3.1.2</a> 允许，则可使用 x264 编码。</strong>
                        </li>
                    </ul>
                </li>
                <li id='r3.3.4'><a href='#h3.3'><strong>&uarr;_</strong></a> <a href='#r3.3.4'>3.3.4.</a> <strong>在电影找不到未有损转码的首选格式时，错误的编解码器、容器和分辨率可能可以容忍。</strong>如果电影能获取到正确格式，则不能再发布错误格式的，除非存在明显的质量提升。见相关的 <a href='#h4.1'>共存</a>／<a href='#h5.2'>替代</a> 规则或到 这里 询问例外情况。
                </li>
                <li id='r3.3.5'><a href='#h3.3'><strong>&uarr;_</strong></a> <a href='#r3.3.5'>3.3.5.</a> 更多关于超高清共存的信息，参见 <a href='#h4.3'>规则相关部分</a>。
                </li>
            </ul>",

    'upload_h34k_note' => "
            <ul>
                <li id='r3.4.1'><a href='#h3.4'><strong>&uarr;_</strong></a> <a href='#r3.4.1'>3.4.1.</a> <strong>原盘种子是与零售光盘完全一致的副本。</strong>它们可能含有菜单、附加内容以及额外的音轨（完整的 VOB_IFO/M2TS 翻录）或是被删减到只剩下电影主体（仅 HD 和 UHD Remux）。Scene NFO 应添加到发布页面的 NFO 区域。仅版权警告部分可被从完整的 VOB_IFO/M2TS 翻录中删减掉。
                <ul>
                    <li id='r3.4.1.1'><a href='#r3.4.1'><strong>&uarr;_</strong></a> <a href='#r3.4.1.1'>3.4.1.1.</a> <strong>防拷贝和地区锁必须被移除。</strong>
                        </li>
                    </ul>
                </li>
                <li id='r3.4.2'><a href='#h3.4'><strong>&uarr;_</strong></a> <a href='#r3.4.2'>3.4.2.</a> <strong>DVD 原盘必须使用 VOB_IFO 容器（VIDEO_TS 文件夹和内容）。</strong>
                </li>
                <li id='r3.4.3'><a href='#h3.4'><strong>&uarr;_</strong></a> <a href='#r3.4.3'>3.4.3.</a> <strong>HDTV 原始抓流必须使用 TS 或 MKV 容器。</strong>
                </li>
                <li id='r3.4.4'><a href='#h3.4'><strong>&uarr;_</strong></a> <a href='#r3.4.4'>3.4.4.</a> <strong>蓝光原盘必须使用 M2TS 容器，除非是 3D Blu-ray Rip，它们应以 ISO 形式发布。</strong>
                <ul>
                    <li id='r3.4.4.1'><a href='#r3.4.4'><strong>&uarr;_</strong></a> <a href='#r3.4.4.1'>3.4.4.1.</a> BD25 存在单碟最大 23.28 GiB 的限制。BD50 存在单碟最大 46.57 GiB 的限制。BD66 存在单碟最大 61.47 GiB 的限制。BD100 存在单碟最大 93.13 GiB 的限制。
                    </li>
                </ul>
                </li>
                <li id='r3.4.5'><a href='#h3.4'><strong>&uarr;_</strong></a> <a href='#r3.4.5'>3.4.5.</a> <strong>Blu-ray Remux 必须使用 MKV 容器。</strong>Remux 种子由原盘（或无损压缩）的音视频组成，简单地混流在一起即可。
                <ul>
                    <li id='r3.4.5.1'><a href='#r3.4.5'><strong>&uarr;_</strong></a> <a href='#r3.4.5.1'>3.4.5.1.</a> <strong>Remux 必须始终使用从源光盘能获取到的最优质量轨道。</strong>
                    </li>
                    <li id='r3.4.5.2'><a href='#r3.4.5'><strong>&uarr;_</strong></a> <a href='#r3.4.5.2'>3.4.5.2.</a> <strong>Remux 必须以下列顺序混流：视频 - 主音轨 (标为默认) - 次音轨 - 字幕</strong>
                    </li>
                    <li id='r3.4.5.3'><a href='#r3.4.5'><strong>&uarr;_</strong></a> <a href='#r3.4.5.3'>3.4.5.3.</a> <strong>PCM 和 DTS-HD MA 2.0 及以下的音轨必须转码到 FLAC，请勿将 24 bit DTS-HD MA 转换成 16 bit。PCM 2.1 及以上必须转码到 DTS-HD MA 或 FLAC。</strong>
                    </li>
                    <li id='r3.4.5.4'><a href='#r3.4.5'><strong>&uarr;_</strong></a> <a href='#r3.4.5.4'>3.4.5.4.</a> <strong>Remux 中允许存在 SRT 字幕。</strong>
                    </li>
                    <li id='r3.4.5.5'><a href='#r3.4.5'><strong>&uarr;_</strong></a> <a href='#r3.4.5.5'>3.4.5.5.</a> <strong>仅适用于动漫：可以包括一条日语音轨和一条英语音轨（不包含评论）。</strong>非动漫的外语内容会被按照双音轨规则处理，见 <a href='#r4.6.2'>4.6.2</a>。
                    </li>
                    <li id='r3.4.5.6'><a href='#r3.4.5'><strong>&uarr;_</strong></a> <a href='#r3.4.5.6'>3.4.5.6.</a> <strong>不允许不带英文字幕的非英文评论音轨。</strong>
                    </li>
                    <li id='r3.4.5.7'><a href='#r3.4.5'><strong>&uarr;_</strong></a> <a href='#r3.4.5.7'>3.4.5.7.</a> <strong>MKV 容器规则的一个例外是杜比视界 Remux。</strong>仅此类 Remux 可以 MP4 容器的形式存在。
                    </li>
                </ul>
                </li>
                <li id='r3.4.6'><a href='#h3.4'><strong>&uarr;_</strong></a> <a href='#r3.4.6'>3.4.6.</a> <strong>仅包含额外素材的原盘必须与主碟制成单个种子发布。</strong>
                </li>
                <li id='r3.4.7'><a href='#h3.4'><strong>&uarr;_</strong></a> <a href='#r3.4.7'>3.4.7.</a> 更多关于原盘种子共存的信息，参见 <a href='#h4.4'>规则相关部分</a>。
                </li>
            </ul>",

    'upload_h35k_note' => "
            <ul>
                <li id='r3.5.1'><a href='#h3.5'><strong>&uarr;_</strong></a> <a href='#r3.5.1'>3.5.1.</a> <strong>附加内容是包含在电影官方发行中的视频材料，但不是电影主体的任何一个版本（幕后花絮、采访……）。</strong>
                <ul>
                        <li id='r3.5.1.1'><a href='#r3.5.1'><strong>&uarr;_</strong></a> <a href='#r3.5.1.1'>3.5.1.1.</a> <strong>附加内容种子必须在发布页面勾选 “不是电影主体” 选项以标记。</strong>
                        </li>
                    </ul>
                </li>
                <li id='r3.5.2'><a href='#h3.5'><strong>&uarr;_</strong></a> <a href='#r3.5.2'>3.5.2.</a> <strong>附加内容仅在其包含于任一官方零售发行的完整版时允许发布。</strong>
                <ul>
                        <li id='r3.5.2.1'><a href='#r3.5.2'><strong>&uarr;_</strong></a> <a href='#r3.5.2.1'>3.5.2.1.</a> <strong>附加内容必须以经销商／版本指明来源，见规则 <a href='#r2.5.2'>2.5.2</a> 以了解更多关于版本信息的内容。</strong>
                        </li>
                    </ul>
                </li>
                <li id='r3.5.3'><a href='#h3.5'><strong>&uarr;_</strong></a> <a href='#r3.5.3'>3.5.3.</a> <strong>仅包含附加内容的光盘必须与主体光盘制成同一个种子一起发布。</strong>
                </li>
                <li id='r3.5.4'><a href='#h3.5'><strong>&uarr;_</strong></a> <a href='#r3.5.4'>3.5.4.</a> <strong>拥有 IMDb 页面的附加内容必须单独发布。</strong>
                </li>
                <li id='r3.5.5'><a href='#h3.5'><strong>&uarr;_</strong></a> <a href='#r3.5.5'>3.5.5.</a> 更多关于附加内容种子共存的信息，参见 <a href='#h4.5'>规则相关部分</a>。
                </li>
            </ul>",



    'upload_h41k_note' => "
            <ul>
                <li id='r4.1.1'><a href='#h4.1'><strong>&uarr;_</strong></a> <a href='#r4.1.1'>4.1.1.</a> <strong>对于给定电影，两个质量不同的标清 x264 Encode 可以共存。</strong>
                <ul>
                        <li id='r4.1.1.1'><a href='#r4.1.1'><strong>&uarr;_</strong></a> <a href='#r4.1.1.1'>4.1.1.1.</a> <strong>两者中其中一者的编码应趋向压缩程度更高、更紧凑（存档导向槽位），而另一者应趋向尽可能高的质量（质量导向槽位）。</strong>作为参考，紧凑的编码应至少比高质量编码小 40%，如此才能共存。
                        </li>
                    </ul>
                </li>
                <li id='r4.1.2'><a href='#h4.1'><strong>&uarr;_</strong></a> <a href='#r4.1.2'>4.1.2.</a> <strong>另外，对于高清源的 x264 576p 编码只留有一个槽位。</strong>它与规则 <a href='#r4.1.1'>4.1.1</a> 所定义的槽位相独立，且互不干涉。该 576p 槽位应留给尽可能高质量的编码。
                </li>
                <li id='r4.1.3'><a href='#h4.1'><strong>&uarr;_</strong></a> <a href='#r4.1.3'>4.1.3.</a> 更多关于标清发布的信息，参见 <a href='#h3.1'>规则相关部分</a>。
                </li>
            </ul>",

    'upload_h42k_note' => "
            <ul>
                <li id='r4.2.1'><a href='#h4.2'><strong>&uarr;_</strong></a> <a href='#r4.2.1'>4.2.1.</a> <strong>对于给定电影，两个质量不同的 720p 和两个质量不同的 1080p x264 Encode 可以共存。</strong>
                <ul>
                        <li id='r4.2.1.1'><a href='#r4.2.1'><strong>&uarr;_</strong></a> <a href='#r4.2.1.1'>4.2.1.1.</a> <strong>每组中其中一者的编码应趋向压缩程度更高、更紧凑（存档导向槽位），而另一者应趋向尽可能高的质量（质量导向槽位）。</strong>作为参考，紧凑的编码应至少比高质量编码小 20%，如此才能共存。
                        </li>
                    </ul>
                </li>
                <li id='r4.2.2'><a href='#h4.2'><strong>&uarr;_</strong></a> <a href='#r4.2.2'>4.2.2.</a> <strong>另外，还有一个额外的槽位留给 HDR x265 1080p Encode。</strong>它与规则 <a href='#r4.2.1'>4.2.1</a> 所定义的槽位相独立，且互不干涉。该槽位应留给尽可能高质量的编码。
                </li>
                <li id='r4.2.3'><a href='#h4.2'><strong>&uarr;_</strong></a> <a href='#r4.2.3'>4.2.3.</a> 更多关于高清发布的信息，参见 <a href='#h3.2'>规则相关部分</a>。
                </li>
            </ul>",

    'upload_h43k_note' => "
            <ul>
                <li id='r4.3.1'><a href='#h4.3'><strong>&uarr;_</strong></a> <a href='#r4.3.1'>4.3.1.</a> <strong>对于给定电影，有两个 2160p Encode 可以共存。</strong>
                <ul>
                        <li id='r4.3.1.1'><a href='#r4.3.1'><strong>&uarr;_</strong></a> <a href='#r4.3.1.1'>4.3.1.1.</a> <strong>每组中其中一者的编码应趋向压缩程度更高、更紧凑（存档导向槽位），而另一者应趋向尽可能高的质量（质量导向槽位）。</strong>作为参考，紧凑的编码应至少比高质量编码小 20%，如此才能共存。
                        </li>
                        <li id='r4.3.1.2'><a href='#r4.3.1'><strong>&uarr;_</strong></a> <a href='#r4.3.1.2'>4.3.1.2.</a> <strong>如果提供了足够多的对比截图证明其优于既存的高清源，则一个 SDR 发行可占据规则 <a href='#r4.3.1.1'>4.3.1.1</a> 定义的较低品质的槽位。</strong>
                        </li>
                    </ul>
                </li>
                <li id='r4.3.2'><a href='#h4.3'><strong>&uarr;_</strong></a> <a href='#r4.3.2'>4.3.2.</a> 更多关于超高清发布的信息，参见 <a href='#h3.3'>规则相关部分</a>。
                </li>
            </ul>",

    'upload_h44k_note' => "
            <ul>
                <li id='r4.4.1'><a href='#h4.4'><strong>&uarr;_</strong></a> <a href='#r4.4.1'>4.4.1.</a> <strong>允许存在一个 NTSC DVD 原盘种子和一个 PAL DVD 原盘种子。两个槽位都应以能获取到的最优质源占据，由管理决定。</strong>
                </li>
                <li id='r4.4.2'><a href='#h4.4'><strong>&uarr;_</strong></a> <a href='#r4.4.2'>4.4.2.</a> <strong>允许存在一个高清原盘种子和一个超高清原盘种子。</strong>两个槽位都应以能获取到的最优质源占据，由管理决定。
                </li>
                <li id='r4.4.3'><a href='#h4.4'><strong>&uarr;_</strong></a> <a href='#r4.4.3'>4.4.3.</a> <strong>允许存在一个高清 Remux 和一个超高清 Remux。</strong>两个槽位都应以能获取到的最优质源占据，由管理决定。
                </li>
                <li id='r4.4.4'><a href='#h4.4'><strong>&uarr;_</strong></a> <a href='#r4.4.4'>4.4.4.</a> 更多关于原盘发布的信息，参见 <a href='#h3.4'>规则相关部分</a>。
                </li>
            </ul>",

    'upload_h45k_note' => "
            <ul>
                <li id='r4.5.1'><a href='#h4.5'><strong>&uarr;_</strong></a> <a href='#r4.5.1'>4.5.1.</a> <strong>每种分辨率（SD、720p、1080p+ Remux）允许存在一个含附加内容的合集。</strong>
                </li>
                <li id='r4.5.2'><a href='#h4.5'><strong>&uarr;_</strong></a> <a href='#r4.5.2'>4.5.2.</a> <strong>假设内容存在实际区别，则来自不同版本的附加内容合集可以共存。</strong>若无区别，该槽位应留给最完整的合集。
                </li>
            </ul>",
    'upload_h46k_note' => "
            <ul>
                <li id='r4.6.1'><a href='#h4.6'><strong>&uarr;_</strong></a> <a href='#r4.6.1'>4.6.1.</a> <strong>允许电影的每种剪辑版本（影院／导演、限制级／未分级……）拥有一组质量导向的槽位。</strong>
                </li>
                <li id='r4.6.2'><a href='#h4.6'><strong>&uarr;_</strong></a> <a href='#r4.6.2'>4.6.2.</a> <strong>非英语电影的英语配音种子（双音轨更好）被视为一个独立版本，因此允许它拥有自己的槽位。</strong>
                </li>
                <li id='r4.6.3'><a href='#h4.6'><strong>&uarr;_</strong></a> <a href='#r4.6.3'>4.6.3.</a> <strong>虽然对于给定电影，每个种子都应源自被视为最佳的版本，但第二组槽位可以作为例外提供给源自稍差但提供了不同观赏体验的版本的种子。</strong>该槽位组通常由每个分辨率的一个 Encode、一个 Remux 和一个完整的光盘副本组成（这里没有存档导向的槽位）。
                </li>
            </ul>",

    'upload_h51k_note' => "
            <ul>
                <li id='r5.1.1'><a href='#h5.1'><strong>&uarr;_</strong></a> <a href='#r5.1.1'>5.1.1.</a> <strong>对于标清种子通常的替代顺序如下：VHS < TV < HDTV | WEB < DVD < Blu-ray。对于高清和超高清种子通常的替代顺序如下：HDTV < WEB | HD-DVD | Blu-ray。</strong>
                    <ul>
                        <li id='r5.1.1.1'><a href='#r5.1.1'><strong>&uarr;_</strong></a> <a href='#r5.1.1.1'>5.1.1.1.</a> <strong>虽说这个替代顺序一般都没问题，但请注意决定的作出最终要落实在质量上（比如，如果 Blu-ray 源被发现是次品，则 WEB Encode 就不会被删除）。</strong>
                        </li>
                    </ul>
                </li>
                <li id='r5.1.2'><a href='#h5.1'><strong>&uarr;_</strong></a> <a href='#r5.1.2'>5.1.2.</a> <strong>未经删减的原盘种子总是能替代相同质量的、抛弃部分内容（比如附加内容或菜单）的种子。</strong>
                </li>
            </ul>",

    'upload_h52k_note' => "
            <ul>
                <li id='r5.2.1'><a href='#h5.2'><strong>&uarr;_</strong></a> <a href='#r5.2.1'>5.2.1.</a> <strong>任何未满足 <a href='#h3'>本文</a> 定义的首选格式的种子都可被符合推荐格式的种子替代，只要其质量同等或更优。</strong>
                    <ul>
                        <li id='r5.2.1.1'><a href='#r5.2.1'><strong>&uarr;_</strong></a> <a href='#r5.2.1.1'>5.2.1.1.</a> <strong>x264 (SD, HD) 和 x265 (UHD) 被视为首选编码器。</strong>来源信息未知的 H.264 或 H.265 文件可以占据规则 <a href='#r4.1.1'>4.1.1</a>、<a href='#r4.2.1'>4.2.1</a> 和 <a href='#r4.3.1'>4.3.1</a> 定义的 x264 或 x265 槽位，但更容易因质量原因被替代。
                        </li>
                    </ul>
                </li>
                <li id='r5.2.2'><a href='#h5.2'><strong>&uarr;_</strong></a> <a href='#r5.2.2'>5.2.2.</a> <strong>占据了规则 <a href='#r4.1.1.1'>4.1.1.1</a>、<a href='#r4.2.1.1'>4.2.1.1</a> 和 <a href='#r4.3.1.1'>4.3.1.1</a> 定义的高质量槽位的种子可被质量显著更佳的 Encode 替代。</strong>
                </li>
                <li id='r5.2.3'><a href='#h5.2'><strong>&uarr;_</strong></a> <a href='#r5.2.3'>5.2.3.</a> <strong>可替代种子可以被无标记所指问题的种子替代。</strong>见规则 <a href='#h5.4'>5.4</a> 了解完整的可替代标记清单。
                </li>
                <li id='r5.2.4'><a href='#h5.2'><strong>&uarr;_</strong></a> <a href='#r5.2.4'>5.2.4.</a> <strong>源（原盘、Remux）类种子易受激烈的替代政策影响，提供更好观影体验者可替代较劣者。</strong>
                </li>
                <li id='r5.2.5'><a href='#h5.2'><strong>&uarr;_</strong></a> <a href='#r5.2.5'>5.2.5.</a> <strong>质量替代（对于 Encode 和源）应尽可能多地通过截图对比来证明改进。</strong>
                </li>
                <li id='r5.2.6'><a href='#h5.2'><strong>&uarr;_</strong></a> <a href='#r5.2.6'>5.2.6.</a> <strong>如果出现重大缺陷（不完整、音轨不同步、错误的纵横比……），则 nuked Scene 发行可自动被 REPACK 或 PREPER 替代掉。不是因影响观影体验的问题（被盗的源、重复、命名错误）而 nuke 的 Scene 发行不是可替代的。</strong>
                </li>
                <li id='r5.2.7'><a href='#h5.2'><strong>&uarr;_</strong></a> <a href='#r5.2.7'>5.2.7.</a> <strong>Remux 可被同等质量但更为完整的种子替代，下列是可能的替代原因：</strong>
                    <ul>
                        <li id='r5.2.7.1'><a href='#r5.2.7'><strong>&uarr;_</strong></a> <a href='#r5.2.7.1'>5.2.7.1.</a> 包含了旧种所不包含的章节信息。
                        </li>
                        <li id='r5.2.7.2'><a href='#r5.2.7'><strong>&uarr;_</strong></a> <a href='#r5.2.7.2'>5.2.7.2.</a> 添加了评论或独立的配乐。
                        </li>
                        <li id='r5.2.7.3'><a href='#r5.2.7'><strong>&uarr;_</strong></a> <a href='#r5.2.7.3'>5.2.7.3.</a> 以适当的相同内容的无损替换旧 Remux 的 PCM 音轨。
                        </li>
                        <li id='r5.2.7.4'><a href='#r5.2.7'><strong>&uarr;_</strong></a> <a href='#r5.2.7.4'>5.2.7.4.</a> 包含了旧种所不包含的中文 PGS/SUP 字幕。
                        </li>
                        <li id='r5.2.7.5'><a href='#r5.2.7'><strong>&uarr;_</strong></a> <a href='#r5.2.7.5'>5.2.7.5.</a> 在管理批准的情况下，源于另一独特母带源的素材允许共存。
                        </li>
                    </ul>
                </li>
            </ul>",

    'upload_h53k_note' => "
            <ul>
                <li id='r5.3.1'><a href='#h5.3'><strong>&uarr;_</strong></a> <a href='#r5.3.1'>5.3.1.</a> <strong>任何不活跃超过 6 周将自动可替代。</strong>
                </li>
                <li id='r5.3.2'><a href='#h5.3'><strong>&uarr;_</strong></a> <a href='#r5.3.2'>5.3.2.</a> <strong>任何发布 24 小时后仍未做种的种子会被自动删除。</strong>
                </li>
                <li id='r5.3.3'><a href='#h5.3'><strong>&uarr;_</strong></a> <a href='#r5.3.3'>5.3.3.</a> <strong>如有可能，尽量为不活跃种子（死种）续种而不是替代之。</strong>
                </li>
            </ul>",

    'upload_h54k_note' => "<ul>
                <li id='r5.4.1'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.1'>5.4.1.</a> <strong>问题纵横比：</strong>编码错误是导致种子表现出错误纵横比的原因。
                </li>
                <li id='r5.4.2'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.2'>5.4.2.</a> <strong>非原始纵横比：</strong>该种子的纵横比与原始的、影院上映的电影不同，一旦存在纵横比正确的发行，同分辨率组内就不允许共存非原始纵横比的种子了。
                </li>
                <li id='r5.4.3'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.3'>5.4.3.</a> <strong>无谓高码：</strong>种子的视频或音频比特率过高。音频比特率上限手册：
                <table border='1'>
                <tr>
                    <td colspan='2' rowspan='2'>源音频</td>
                    <td colspan='4'>Encode</td>
                    <td colspan='2'>Remux</td>
                </tr>
                <tr>
                    <td>SD</td>
                    <td>720p</td>
                    <td>1080p</td>
                    <td>2160p</td>
                    <td>1080p</td>
                    <td>2160p</td>
                </tr>
                <tr>
                    <td rowspan='3'>主音轨</td>
                    <td>7.1/5.1 无损</td>
                    <td>640 kbps AC3 (首选 448 kbps AC3)</td>
                    <td>1509 kbps AC3 (首选 640  kbps AC3)</td>
                    <td>1536 kbps E-AC3</td>
                    <td>保持原样</td>
                    <td>保持原样</td>
                    <td>保持原样</td>
                </tr>
                <tr>
                    <td>2.0/1.0 无损</td>
                    <td>16-bit FLAC (首选高质量 AAC)</td>
                    <td>16-bit FLAC (首选高质量 AAC)</td>
                    <td>16-bit FLAC</td>
                    <td>保持原样 (首选 24-bit FLAC)</td>
                    <td>保持原样 (首选 FLAC)</td>
                    <td>保持原样 (首选 FLAC)</td>
                </tr>
                <tr>
                    <td>有损</td>
                    <td>保持原样</td>
                    <td>保持原样</td>
                    <td>保持原样</td>
                    <td>保持原样</td>
                    <td>保持原样</td>
                    <td>保持原样</td>
                </tr>
                <tr>
                    <td rowspan='2'>次音轨</td>
                    <td>无损</td>
                    <td>中质量 AAC</td>
                    <td>高质量 AAC</td>
                    <td>16-bit FLAC</td>
                    <td>16-bit FLAC</td>
                    <td>保持原样 (首选 FLAC)</td>
                    <td>保持原样 (首选 FLAC)</td>
                </tr>
                <tr>
                    <td>有损</td>
                    <td>如果高于 192 kbps 就采用中质量 AAC</td>
                    <td>如果高于 192 kbps 就采用中质量 AAC</td>
                    <td>16-bit FLAC (首选高质量 AAC)</td>
                    <td>16-bit FLAC</td>
                    <td>保持原样</td>
                    <td>保持原样</td>
                </tr>
            </table>
                </li>
                <li id='r5.4.4'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.4'>5.4.4.</a> <strong>音轨冗余：</strong>种子包含多余的音轨，有如非英语配音或同一音轨的冗余版本。
                </li>
                <li id='r5.4.5'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.5'>5.4.5.</a> <strong>反交错问题：</strong>种子被错误地反交错了。
                </li>
                <li id='r5.4.6'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.6'>5.4.6.</a> <strong>帧率错误：</strong>种子以不同于原生、正确的帧率播放。
                </li>
                <li id='r5.4.7'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.7'>5.4.7.</a> <strong>字幕不同步：</strong>种子中包含的字幕有效，但不同步。
                </li>
                <li id='r5.4.8'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.8'>5.4.8.</a> <strong>格式不当：</strong>种子不符合我们的 <a href='#h3'>推荐格式</a>。
                </li>
                <li id='r5.4.9'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.9'>5.4.9.</a> <strong>分辨率不当：</strong>种子不符合我们的 <a href='#h3'>推荐分辨率</a>。
                </li>
                <li id='r5.4.10'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.10'>5.4.10.</a> <strong>劣质源：</strong>种子的源没能提供当下能获取到的最好观影体验。
                </li>
                <li id='r5.4.11'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.11'>5.4.11.</a> <strong>低质量：</strong>种子编码所使用的源非常糟糕，或是受到了重大质量问题的影响。
                </li>
                <li id='r5.4.12'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.12'>5.4.12.</a> <strong>播放问题：</strong>通常会由次级标记详细说明导致种子无法完美播放或编码的问题。
                </li>
                <li id='r5.4.13'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.13'>5.4.13.</a> <strong>残缺：</strong>种子缺失内容，通常会由次级标记详细说明。
                </li>
                <li id='r5.4.14'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.14'>5.4.14.</a> <strong>无英文字幕：</strong>种子是不包含英文字幕（通过字幕管理器内挂或外挂）的非英语电影。
                </li>
                <li id='r5.4.15'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.15'>5.4.15.</a> <strong>未强制英文字幕：</strong>种子的重要非英语对白不包含单独的英文字幕。
                </li>
                <li id='r5.4.16'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.16'>5.4.16.</a> <strong>非英语配音：</strong>种子既没有原始语种音频也没有英语配音，只有非英语的配音。
                </li>
                <li id='r5.4.17'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.17'>5.4.17.</a> <strong>音轨不同步：</strong>种子中包含的音轨有效，但不同步。
                </li>
                <li id='r5.4.18'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.18'>5.4.18.</a> <strong>问题裁边：</strong>种子明显裁边过多或过少。
                </li>
                <li id='r5.4.19'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.19'>5.4.19.</a> <strong>劣质字幕翻译：</strong>种子中包含的字幕质量很差，且不是电影的准确翻译。
                </li>
                <li id='r5.4.20'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.20'>5.4.20.</a> <strong>硬字幕：</strong>种子中的字幕被硬编码在视频轨中。此标记不针对硬编码强制字幕。
                </li>
                <li id='r5.4.21'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.21'>5.4.21.</a> <strong>PAL 加速：</strong>种子是由加速了的 PAL 源编码的。
                </li>
                <li id='r5.4.22'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.22'>5.4.22.</a> <strong>有损转码：</strong>种子的视频或音频轨编码自已有损压缩的源。
                </li>
                <li id='r5.4.23'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.23'>5.4.23.</a> <strong>含水印：</strong>种子含有明显的水印。
                </li>
                <li id='r5.4.24'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.24'>5.4.24.</a> <strong>放大：</strong>种子编码自低清源。
                </li>
                <li id='r5.4.25'><a href='#h5.4'><strong>&uarr;_</strong></a> <a href='#r5.4.25'>5.4.25.</a> <strong>不活跃：</strong>种子无人做种已达至少 6 周。这个标记是自动添加和去除的。
                </li>
            </ul>",









    'chat_title' => "社交",
    'chat_forum' => "论坛规则不允许的行为在交流群也不允许，反之亦然。分开来写仅仅是为了方便。",
    'chat_forums' => "论坛规则",
    'chat_forums_rules' => "<li>
            论坛的每个版块（有如严肃讨论、水区等）有其自己的额外规定。在其中发帖前请务必确保自己已经阅读并了解了规则的相关内容。
        </li>
        <li>
            如果是英文标题请不要全部大写，以及不要使用过多的！！！（感叹号）或？？？（问号）。这会让你看起来像是在大吼大叫一样。
        </li>
        <li>
            不允许带有获利目的。这包含了任何能够使发帖者通过其他用户点击链接来获利的网站或其他类似计划。
        </li>
        <li>
            无论如何，不允许因任何理由索要金钱。尽管我们很在乎失去了一切的好朋友或是只剩下几个月生命想要享受富裕生活的濒死亲戚，然而，<?= (SITE_NAME) ?> 并不能满足你的这种需求。
        </li>
        <li>
            不要不当地宣传你发布的资源。在特殊情况下，你可以去我们许可的地方聊聊你新发的好东西（例如 <a href='forums.php?action=viewthread&amp;threadid=841'>发帖谈谈你的首个种子以便大家下载</a>），但请务必保证在发帖前认真阅读了相关板块的规定。另外，单纯聊你所发布专辑中的音乐也是被允许的。而在恰当的版块、帖子以外的地方大肆宣传你发布的资源会导致被警告或失去论坛相关权限。
        </li>
        <li>
            不要在论坛求种。网站顶部就有求种链接，请善用它。
        </li>
        <li>
            请保持愉悦、礼貌，不要发火。不要用言语冒犯他人，还有，不要以眼还眼，以牙还牙。
        </li>
        <li>
            不要对他人的分享率指指点点。更高的分享率并不能使你高人一等。
        </li>
        <li>
            请尽量不要问些蠢问题。但凡你能花一点点时间搜出来结果的，或是问错了地方的都是蠢问题。如果你已按照建议进行了基本的搜索（例如在规则、百科等页面）或是搜索了论坛却没能找到结果，那便请问吧。管理组和一线支持可没有闲到给你去找稍微动动手就能得到的答案。
        </li>
        <li>
            在论坛发帖前务必保证你已阅读了所有置顶帖子。
        </li>
        <li>
            请使用描述性的、准确的帖子标题。这样其他人就可以根据你的标题判断这是否与他们关心的话题相干了。
        </li>
        <li>
            请尽量不要发表与讨论话题毫不相干的回复。当你只是悠闲地随意浏览时，一堆 “666” 、 “牛啤牛啤” 可能不会很烦人。但是当你需要查找有效信息的时候，那就很讨厌了。因此，让那些无意义的回复退出舞台，并让真正感兴趣的人居于前列吧。
            <p>
                或者，简而言之：拒绝垃圾信息。
            </p>
        </li>
        <li>
            不要过分引用。当你引用他人的话时，请仅仅引用确实必要的一小部分，尤其是避免引用图片！
        </li>
        <li>
            不要发帖求连载出版物或是破解软件。不要在论坛里发 WareZ 或是破解网站的链接。
        </li>
        <li>
            不要讨论政治和宗教。此类讨论会导致用户们吵得面红耳赤、不可开交，这是我们绝对不能容忍的。此规则的唯一例外是严肃讨论版块，该版块仅用于智力和文明的论辩。
        </li>
        <li>
            不要通过发布大图来浪费他人的带宽。
        </li>
        <li>
            对新人要有耐心。有时你会忘记在成为大佬之前也是从萌新开始的，如果可以的话，请尽量帮助新人。
        </li>
        <li>
            在网站、交流群的任何地方都不能求邀，我们仅在论坛的邀请版块（仅限一定级别以上的用户访问）允许求邀。
        </li>
        <li>
            聊天板块中有一些特定语言的帖子。除此之外，论坛中不允许使用中文、英文以外的语言，因为我们看不懂就没法加以管理。一些一线支持掌握了其他语种，如果你有疑问，且不善于使用中文表达，可以私信他们。
        </li>
        <li>
            在论坛里发表成人内容时请小心一些。所有成人图像都必须遵守 <a href='wiki.php?action=article&amp;id=145'>这些规则</a>。发表涉及超出容忍范围的性与暴力内容的帖子会导致被警告或更严重的后果。
        </li>
        <li>
            帖子中的成人内容必须正确标记。正确的格式如下：<strong>[mature=描述] ……内容…… [/mature]</strong>，其中 “描述” 是对帖子内容的强制性描述。错误或是不充分的描述会导致惩罚。
        </li>
        <li>
            专门为发布成人内容所创建的主题会被删除。成人内容（包括写真封面）应与你在论坛中发布的帖子在内容上相关。如果你对帖子是否合适不太确定，请向论坛管理员 <a href='staffpm.php'>发送私信</a> 并在进一步操作之前等待回复。
        </li>",
    'chat_forums_irc' => "<li>
            管理组拥有最终裁决权。若管理员喊停而你没有遵从，被踢出交流群只是最轻的惩罚。
        </li>
        <li>尊重交流群的群主和管理员。他们志愿无偿为管理站点付出时间和精力并致力于造福大众并化解冲突。请勿浪费他们的宝贵时间。
        </li>
        <li>严禁不经高能预警就发送恐怖网站的链接或是任何不适合在工作场合出现的内容（常指于互联网上的各种诸如电邮、影片或互动媒体（如：讨论区、博客或各种社交网络服务网站）上出现的超链接，当中某些不适合上班时段观看、可能会冒犯上司或同事的内容，多指裸露、暴力、色情或冒犯等不适宜公众场合的内容。）如果不能肯定，请先私戳管理员询问。
        </li>
        <li>
            毫无节制的谩骂将导致你被踢出交流群，请尽量克制自己。
        </li>
        <li>
            To English users：Do not leave Caps Lock enabled all the time. It gets annoying, and you will likely get yourself kicked.
        </li>
        <li>
            不要争吵。在互联网上你不可能真正胜利，你只是在浪费时间而已。
        </li>
        <li>
            拒绝歧视，特别是与人种、宗教、政治、性取向、族裔背景等相关的。强烈建议完全回避这些话题。
        </li>
        <li>
            刷屏会导致你被踢。这包括但不限于自动播放脚本、大量的复制粘贴以及与当前话题无关的多条连续消息。
        </li>
        <li>假装其他用户——尤其是伪装管理员——绝对会被秋后算账。如果你不能肯定某个用户的身份，请先查看其个人资料。
        </li>
        <li>
            严厉禁止垃圾信息，包括但不限于在个人页面、在线拍卖、发布的种子中夹带私货。
        </li>
        <li>
            恶意骚扰——无论是对其他用户还是管理成员——都是不可饶恕的。
        </li>
        <li>
            未经询问，请勿私信或搜索任何你不认识或是从未交谈过的人，尤其是管理员。
        </li>
        <li>
            在群里交流时请使用中文或英文，我们无法对不认识的语言进行有效管理。
        </li>
        <li>
            在群里提供、兜售、交易或赠送本站及他站的邀请是被<strong>严厉禁止</strong>的。
        </li>
        <li>
            禁止私下组织未经许可的与本站相关的交流群（比如面试试题讨论等）。
        </li>
        <li><strong>在问问题前先看看群公告。</strong>
        </li>",
    'chat_groups' => "交流群规则",

    'tags_title' => "标签",
    'tags_summary' => "
        <li>标签应以英文逗号（ “,” ）分隔，你应使用英文点号（ “.” ）来分隔标签内的单词——例如 “<strong class='important_text_alt'>hip.hop</strong>”。</li>

        <li>请使用<a href='upload.php'>左侧文本框的官方标签</a>，而不是 “非官方” 标签（例如使用官方的 “<strong class='important_text_alt'>drum.and.bass</strong>” 标签，而不是非官方的  “<strong class='important_text'>dnb</strong>” 标签）。<strong>请注意 “<strong class='important_text_alt'>2000s</strong>” 表示 2000 到 2009 之间。</strong></li>

        <li>尽量避免缩写。所以不要将专辑的标签缩写为 “<strong class='important_text'>alt</strong>” ，而是应该添加 “<strong class='important_text_alt'>alternative</strong>” ，请确保拼写正确。</li>

        <li>避免使用多个同义标签。使用 “<strong class='important_text'>prog.rock</strong>” 和 “<strong class='important_text_alt'>progressive.rock</strong>” 是同一个意思，不仅多余而且烦人，需使用官方的 “<strong class='important_text_alt'>progressive.rock</strong>” 标签。</li>

        <li>不要添加 “无用” 的标签，如 “<strong class='important_text'>seen.live</strong>” 、 “<strong class='important_text'>awesome</strong>” 、 “<strong class='important_text'>rap</strong>” （包含在 “<strong class='important_text_alt'>hip.hop</strong>” ）等。如果是现场专辑，你可以添加 “<strong class='important_text_alt'>live</strong>”。</li>

        <li>仅添加专辑本身的信息，而不是某个具体版本的信息</strong>。严禁使用 “<strong class='important_text'>v0</strong>” 、 “<strong class='important_text'>eac</strong>” 、 “<strong class='important_text'>vinyl</strong>” 、 “<strong class='important_text'>from.what</strong>” 等。请记住，他们仅用以标明同一专辑的其他版本，本身并非标签。</li>

        <li><strong>如果你对<a href='upload.php'>左侧文本框的官方标签</a>有疑问，那就不要添加进去。</strong></li>",
    'tags_summary_onupload' => "
        <li>标签应以英文逗号（ “,” ）分隔，你应使用英文点号（ “.” ）来分隔标签内的单词——例如 “<strong class='important_text_alt'>hip.hop</strong>”。</li>

        <li>请使用左侧文本框的官方标签，而不是 “非官方” 标签（例如使用官方的 “<strong class='important_text_alt'>drum.and.bass</strong>” 标签，而不是非官方的  “<strong class='important_text'>dnb</strong>” 标签）。<strong>请注意 “<strong class='important_text_alt'>2000s</strong>” 表示 2000 到 2009 之间。</strong></li>

        <li>尽量避免缩写。所以不要将专辑的标签缩写为 “<strong class='important_text'>alt</strong>” ，而是应该添加 “<strong class='important_text_alt'>alternative</strong>” ，请确保拼写正确。</li>

        <li>避免使用多个同义标签。使用 “<strong class='important_text'>prog.rock</strong>” 和 “<strong class='important_text_alt'>progressive.rock</strong>” 是同一个意思，不仅多余而且烦人，需使用官方的 “<strong class='important_text_alt'>progressive.rock</strong>” 标签。</li>

        <li>不要添加 “无用” 的标签，如 “<strong class='important_text'>seen.live</strong>” 、 “<strong class='important_text'>awesome</strong>” 、 “<strong class='important_text'>rap</strong>” （包含在 “<strong class='important_text_alt'>hip.hop</strong>” ）等。如果是现场专辑，你可以添加 “<strong class='important_text_alt'>live</strong>”。</li>

        <li>仅添加专辑本身的信息，而不是某个具体版本的信息</strong>。严禁使用 “<strong class='important_text'>v0</strong>” 、 “<strong class='important_text'>eac</strong>” 、 “<strong class='important_text'>vinyl</strong>” 、 “<strong class='important_text'>from.what</strong>” 等。请记住，他们仅用以标明同一专辑的其他版本，本身并非标签。</li>

        <li><strong>如果你对左侧文本框的官方标签有疑问，那就不要添加进去。</strong></li>",

    'upload_title_de' => "该部分规则决定哪些内容可以被发布到本站。",
    'clients_title_de' => "该部分规则决定哪些客户端可以连接到我们的服务器，以及为它们设定的相关条例。",
    'chat_title_de' => "该部分规则请你在前往论坛发帖或交流群发言之前阅读。",
    'tags_title_de' => "该部分规则决定哪些标签可以添加而哪些不能。",
    'collages_title_de' => "该部分规则决定合集的组织和管理形式。",
    'requests_title_de' => "该部分规则决定求种的组织和管理形式。",
    'ratio_title_de' => "该部分规则决定用户在本站做种／下载活动应如何进行。",
    'golden_rules_de' => "该部分规则至关重要，违反它们会导致极为严重的后果。",
    'end' => "分享率规则"
);
