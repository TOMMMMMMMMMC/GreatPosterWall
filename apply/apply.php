<?
fromIndex();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= Lang::get('pub', 'apply_to_join') ?> :: Great Poster Wall</title>
    <link rel="stylesheet" href="style/join.css" type="text/css" />
    <script type="text/javascript" src="scripts/gen_validatorv31.js"></script>
    <link rel="STYLESHEET" type="text/css" href="style/pwdwidget.css" />
    <script src="scripts/pwdwidget.js" type="text/javascript"></script>
    <script src="scripts/lang.js" type="text/javascript"></script>
</head>

<body>
    <div id="head">
        <select name="language" id="language" onchange="change_lang(this.options[this.options.selectedIndex].value)">
            <option value="chs" <? if (empty($_COOKIE['lang']) || $_COOKIE['lang'] == 'chs') { ?>selected<? } ?>>简体中文</option>
            <option value="en" <? if (!empty($_COOKIE['lang']) && $_COOKIE['lang'] == 'en') { ?>selected<? } ?>>English</option>
        </select>
    </div>
    <!-- Form Code Start -->
    <div id="container">
        <header>
            <div id="header">
                <img id="logo" src="https://greatposterwall.com/static/styles/public/images/loginlogo.png" />
                <h1 id="web-title"><?= Lang::get('pub', 'apply_to_join') ?></h1>
            </div>
        </header>
        <form id='register' method='post' accept-charset='UTF-8'>
            <div id="main">
                <div class="container">
                    <div class="card" id="intro">
                        <div id="welcome-container">
                            <h5 id="welcome-head"><?= Lang::get('pub', 'welcome_to_dic_s_application_system') ?></h5>
                            <ul>
                                <li><?= Lang::get('pub', 'application_note_1') ?></li>
                                <li><?= Lang::get('pub', 'application_note_2') ?></li>
                                <li><?= Lang::get('pub', 'application_note_3') ?></li>
                                <li><?= Lang::get('pub', 'application_note_4') ?></li>
                                <li><?= Lang::get('pub', 'application_note_5') ?></li>
                                <li><?= Lang::get('pub', 'application_note_6') ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card" id="body">
                        <ol>
                            <li>
                                <h5><?= Lang::get('pub', 'application_email_address') ?></h5>
                                <div id="email-container">
                                    <input type='text' name='email' id='email' maxlength="50" />
                                </div>
                                <span id='register_email_errorloc' class='error'></span>
                            </li>
                            <li>
                                <h5><?= Lang::get('pub', 'application_trackers') ?></h5>
                                <textarea id="site" name="site" rows="6" placeholder="<?= Lang::get('pub', 'application_trackers_placeholder') ?>"></textarea>
                            <li>
                                <h5><?= Lang::get('pub', 'application_screenshots') ?></h5>
                                <ul class="remarks">
                                    <li><?= Lang::get('pub', 'application_screenshots_note_1') ?></li>
                                    <li><?= Lang::get('pub', 'application_screenshots_note_2') ?></li>
                                    <li><?= Lang::get('pub', 'application_screenshots_note_3') ?></li>
                                    <li><?= Lang::get('pub', 'application_screenshots_note_4') ?></li>
                                    <li><?= Lang::get('pub', 'application_screenshots_note_5') ?></li>
                                    <li><?= Lang::get('pub', 'application_screenshots_note_6') ?></li>
                                </ul>
                                <textarea id="screenshot1" name="screenshot1" placeholder="<?= Lang::get('pub', 'application_screenshots_placeholder') ?>" rows="6"></textarea>
                                <!-- 按一下加号增添一个栏位，或者是上面钩了一个选项下边就多一个栏位 -->
                            </li>
                            <li>
                                <h5><?= Lang::get('pub', 'application_clients') ?></h5>
                                <ul class="remarks">
                                    <li><?= Lang::get('pub', 'application_clients_note_1') ?></li>
                                    <li><?= Lang::get('pub', 'application_clients_note_2') ?></li>
                                    <li><?= Lang::get('pub', 'application_clients_note_3') ?></li>
                                    <li><?= Lang::get('pub', 'application_clients_note_4') ?></li>
                                </ul>
                                <textarea id="screenshot2" name="screenshot2" placeholder="<?= Lang::get('pub', 'application_clients_placeholder') ?>" rows="6"></textarea>
                            </li>

                            <li>
                                <h5><?= Lang::get('pub', 'application_introduction') ?></h5>
                                <span class="remarks">
                                    <?= Lang::get('pub', 'application_introduction_note') ?>
                                </span>
                                <ul class="remarks">
                                    <li><?= Lang::get('pub', 'application_introduction_note_1') ?></li>
                                    <li><?= Lang::get('pub', 'application_introduction_note_2') ?></li>
                                    <li><?= Lang::get('pub', 'application_introduction_note_3') ?></li>
                                    <li><?= Lang::get('pub', 'application_introduction_note_4') ?></li>
                                    <li><?= Lang::get('pub', 'application_introduction_note_5') ?></li>
                                </ul>
                                <textarea id="introduction" name="introduction" placeholder="<?= Lang::get('pub', 'application_introduction_placeholder') ?>" v-model="cureInfo.Symptom" oninput="autoTextAreaHeight(this)"></textarea>
                                <!-- <br /> -->

                                <script>
                                    function autoTextAreaHeight(o) {
                                        o.style.height = o.scrollTop + o.scrollHeight + "px";
                                    }
                                    $(function() {
                                        var ele = document.getElementById("introduction");
                                        autoTextAreaHeight(ele);
                                    })
                                </script>
                            </li>
                        </ol>
                        <div align="center" id="apply-body-submit">
                            <input type="hidden" name="action" value="takeapply">
                            <button type="submit" id="submit"><?= Lang::get('pub', 'application_submit') ?></button>
                        </div>
                    </div>
                </div>

        </form>
        <?/*
        <!-- <li>
                    请问你是从何得知我们的入站测试的？
                    <br/>
                    <input id="where" name="where" type="textarea"></li>
                    <li>
                    聊聊你对 DIC 的认识，以及你想要从 DIC 获得什么。
                    <br/>
                    <input id="what" name="what" type="textarea"></li>
                    <li>
                    那么，你愿意为 DIC 提供些什么？你对作为中国大陆首个 gazelle 站点的我们有何期待？
                    <br/>
                    <input id="will" name="will" type="textarea"></li>
                    <li>
                    所谓的“无损音乐”是真“无损”吗？如果你认为是，请说明理由；如果你认为不是，也请说明理由。
                    <br/>
                    <input id="lossless" name="lossless" type="textarea"></li>
                    <li>
                    CD 中的数据能否被精确地复制到电脑硬盘内？如果你认为能，请说明理由；如果你认为不能，也请说明理由。
                    <br/>
                    <input id="cd" name="cd" type="textarea"></li>
                    <li>
                    有位仁兄闲得发慌，将他的音乐文件按如下顺序转码：APE → FLAC → MP3 320K → WAV。此时的文件是无损吗？请说明理由。
                    <br/>
                    <input id="format" name="format" type="textarea">
                    <li>
                    连接性为“否”会带来什么样的影响？遇到这种情况你会怎么处理？
                    <br/>
                    <input id="connactable" name="connactable" type="textarea">
                </li> -->
*/ ?>
    </div>
    <?
    include('footer.php');
    ?>
</body>

</html>