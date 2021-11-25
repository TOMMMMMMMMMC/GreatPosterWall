<?php
$lang_inbox = array(
    'inbox' => "收件箱",
    'sentbox' => "发件箱",
    'your' => "你的",
    'is_empty' => "是空的。",
    'user' => "用户",
    'subject' => "主题",
    'message' => "内容",
    'list_unread_first' => "未读优先",
    'list_latest_first' => "最新优先",
    'mark_as_read' => "标为已读",
    'mark_as_unread' => "标为未读",
    'delete_messages' => "删除信件",
    'receiver' => "收件人",
    'sender' => "发件人",
    'date' => "日期",
    'forwarded_to' => "转发",
    'no_results' => "空空如也。",
    'placeholder_search' => "搜索",

    #conversation.php
    'view_conversation_space' => "对话主题 ",
    'back_to_inbox' => "返回收件箱",
    'quote' => "回复",
    'reply' => "回复",
    'manage_conversation' => "管理对话",
    'sticky' => "置顶对话",
    'delete_conversation' => "删除对话",
    'forward_conversation' => "转发对话",
    'forward_to' => "转发给",

    //compose.php
    'you_cannot_start_a_conversation_with_yourself' => "你不能向自己发起对话！",
    'compose' => "撰写私信",
    'send_a_message_to_user_before' => "向 ",
    'send_a_message_to_user_after' => " 发送私信",
    'body' => "内容",

    //app/bonus.php
    'here_be_fl_tokens_1' => "你",
    'here_be_fl_tokens_2' => " 枚免费令牌",
    'here_be_fl_tokens_3' => "",
    'are' => "收到了",
    's' => "",
    'is' => "收到了",
    'hello_user_before' => "你好，",
    'hello_user_after' => "：",
    'user_has_sent_you' => " 向你赠送了 ",
    'n_fl_tokens' => " 枚免费令牌",
    'for_you_to_use' => " 供你消费！",
    'you_can_use_to_dl' => "你可在下载种子时使用它们，从而避免下载量的增长。",
    'more_details_on' => "关于免费令牌的更多信息请见 ",
    'the_wiki_enjoy' => "wiki.php?action=article&name=免费令牌]这篇 wiki[/url]。

	祝你购物愉快！",

    //sections/schedule/weekly/warn_uploaders.php
    'unseeded_torrent_notification' => "种子未做种通知",
    'unseeded_torrent_notification_1' => "你发布的种子中有 ",
    'unseeded_torrent_notification_2' => " 个很快就会因为不活跃而被删除。种子四周不做种就会被删除。如果你仍然拥有种子内容文件，你可以在客户端确认种子处于做种状态来保证你所发布种子的安全。请通过点开种子详情，查看 “最新活动” 的时间来确认种子未做种的时长。更多信息，请见 [url=" . site_url() . "wiki.php?action=article&amp;id=67]本文[/url]。\n\n以下种子",
    'unseeded_torrent_notification_3' => "将因不活跃而被删除：",
    'unseeded_torrent_notification_4' => "\n\n如果你不愿再接收此类提醒，请前往个人设置关闭。",

    //sections/requests/take_unfil.php
    'request_filled_unfilled' => "你有一个应求被否决",
    'request_created_unfilled' => '你所创建的一个求种的应求被否决',
    'the_request_url' => "求种 \"[url=",
    'url_was_unfilled_by_url' => "[/url]\" 的应求被 [url=",
    'url_for_the_reason_quote' => "[/url] 所否决，理由是：[quote]",
    'quote_if_disagree_unfill_please_url' => "[/quote]\n如果你认为对应求的否决并不合理，请 [url=",
    'report_request_and_explain' => "]报告求种[/url] 并解释它不该被否决的具体原因。",

    //class/donations.class.php
    'get_special_rank_one_sbj' => "你已达到特殊等级 1！你获得了：免疫不活跃。详情请见正文。",
    'get_special_rank_two_sbj' => "你已达到特殊等级 2！你获得了：一枚捐助印记。详情请见正文。",
    'get_special_rank_three_sbj' => "你已达到特殊等级 3！你获得了：一次用户指定。详情请见正文。",
    'get_special_rank_four_sbj' => "你已达到特殊等级 4！你获得了：第二头像。详情请见正文。",
    'get_special_rank_five_sbj' => "你已达到特殊等级 5！你获得了：钻石捐助等级。详情请见正文。",

    'get_special_rank_one_pm' => '恭喜你达到 [url=' . site_url() . 'wiki.php?action=article&id=277]特殊等级 1[/url]！你现在已经能够免疫 [url=' . site_url() . 'wiki.php?action=article&id=73]账号不活跃[/url] 了。祝你玩得开心！
			
	' . SITE_NAME . ' Staff',

    'get_special_rank_two_pm' => '恭喜你达到 [url=' . site_url() . 'wiki.php?action=article&id=277]特殊等级 2[/url]！你获得了一枚 [url=' . site_url() . 'badge.php]捐助纪念印记[/url]。祝你玩得开心！
		
' . SITE_NAME . ' Staff',


    'get_special_rank_three_pm' => '恭喜你达到 [url=' . site_url() . 'wiki.php?action=article&id=277]特殊等级 3[/url]！你赢得了[b]一次用户指定[/b]！你指定的专辑会在 ' . SITE_NAME . ' 首页亮相。在你提交选择后，我们无法保证你选择的专辑的具体上线时间。用户指定专辑将按照先到先得的顺序排列。指定时请遵循以下准则：
[*]请指定尚未当选过推荐专辑的专辑。你可以在论坛里找到过往的推荐专辑。
[*]仔细完整地填写所附表格。
[*]关于你所指定专辑的具体安排、上线时间，请发送 [url=' . site_url() . 'staffpm.php]Staff PM[/url] 询问。
		
' . SITE_NAME . ' Staff',
    'get_special_rank_four_pm' => '恭喜你达到 [url=' . site_url() . 'wiki.php?action=article&id=277]特殊等级 4[/url]！你获得了[b]第二头像[/b]！你可前往设置页面的个人设置部分设定你的第二头像，在鼠标移动到你的头像上时，它就能自动切换显示第二头像。

' . SITE_NAME . ' Staff',
    'get_special_rank_five_pm' => '恭喜你达到 [url=' . site_url() . 'wiki.php?action=article&id=277]特殊等级 5[/url]！你现在是[b]钻石捐助者[/b]了！你已永久激活所有捐助等级所涉及的所有奖励，海豚感谢你的奉献！

' . SITE_NAME . ' Staff',

    'expire_rank_sbj' => "你的捐助等级即将下降",
    'expire_rank_pm' => "你的捐助等级还有两天就会到期，届时，你还会拥有额外两天宽限期，在宽限期过去后，你的捐助等级就会下降一级。",

    //sections/schedule/hourly/promote_users.php
    'promote_pm_subject' => "你现已升级到 ",
    'promote_pm_body' => '恭喜你现已提升到了 ' . Users::make_class_string($L['To']) . " ！\n\n更多关于 " . SITE_NAME . " 用户等级的内容，请阅读 [url=" . site_url() . "wiki.php?action=article&amp;name=userclasses]本文[/url]。",
    'demote_pm_subject' => "你现已降级到 ",
    'demote_pm_body' => "你现在只满足 \"" . Users::make_class_string($L['From']) . "\" 等级的要求。\n\n更多关于 " . SITE_NAME . " 用户等级的内容，请阅读 [url=" . site_url() . "wiki.php?action=article&amp;name=userclasses]本文[/url]。",

    //reportsv2/ajax_take_pm.php
    'torrent_you_uploaded_has_been_reported' => "你发布的 [url=" . site_url() . "torrents.php?torrentid=$TorrentID]这个种子[/url] 被报告了，理由是：" . $ReportType['title'] . "。\n\n$Message",
    'pm_uploader' => "这封私信是发给作为 [url=" . site_url() . "torrents.php?torrentid=$TorrentID]这个种子[/url] 发布者的你的。\n\n$Message",
    'pm_reporter' => "你报告了 [url=" . site_url() . "torrents.php?torrentid=$TorrentID]这个种子[/url]，理由是 " . $ReportType['title'] . "：\n[quote]" . $_POST['report_reason'] . "[/quote]\n$Message",
    'sth_went_wrong' => "出了大问题了",
    'non_number_present' => "这个数字不存在喂",
    'that_is_you' => "可不就是你本人嘛！",


    //classes/torrents.class.php
    'torrent_deleted_colon' => "种子被删除：",
    'a_torrent_space' => "一个",
    'space_has_been_trumped_new_url' => '已被替代。你可以点击 [url=' . site_url() . 'torrents.php?torrentid=' . $TrumpID . ']这里[/url] 查看新种子。',
    'space_has_been_deleted' => "已被删除。",
    'log_message' => "站点日志",
    'message_from_before' => "来自 ",
    'message_from_after' => " 的消息",
    'you_uploaded' => "你发布的种子",
    'you_re_seeding' => "你正做种的种子",
    'you_ve_snatched' => "你已完成的种子",
    'you_ve_downloaded' => "你已下载的种子",


    //schedule/daily/demote_users.php
    'you_have_been_demoted_to_1' => "你已降级至 ",
    'you_have_been_demoted_to_2' => "你现在只满足 “",
    'you_have_been_demoted_to_3' => "” 用户等级的要求。\n\n欲了解更多关于 " . SITE_NAME . " 用户等级的知识，请阅读 [url=" . site_url() . "wiki.php?action=article&amp;name=userclasses]本文[/url]。",

    //schedule/daily/promote_users.php
    'you_have_been_promoted_to_1' => "你已升级至 ",
    'you_have_been_promoted_to_2' => "恭喜你晋升到 “",
    'you_have_been_promoted_to_3' => "”！\n\n欲了解更多关于 " . SITE_NAME . " 用户等级的知识，请阅读 [url=" . site_url() . "wiki.php?action=article&amp;name=userclasses]本文[/url]。",
    'you_have_been_demoted_to_4' => "你现在只满足 “",

    //takecompose.php
    'a_recipient_does_not_exist' => "收件人不存在。",
    'this_recipient_does_not_exist' => "收件人不存在。",
    'cannot_send_msg_without_subject' => "你不能发送没有主题的私信。",
    'cannot_send_msg_without_body' => "你不能发送没有内容的私信。",
);
