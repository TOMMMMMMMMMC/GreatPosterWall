<?
//ini_set('max_file_uploads', 1);
View::show_header(Lang::get('subtitles', 'h2_subtitles'), 'validate_subtitles');
?>




<div class="thin">
    <div class="header">
        <h2><?= Lang::get('subtitles', 'h2_subtitles') ?></h2>
    </div>
    <div class="linkbox">
        <a href="subtitles.php?action=new" class="brackets"><?= Lang::get('subtitles', 'new_subtitle') ?></a>
        <a href="subtitles.php?action=new" class="brackets"><?= Lang::get('subtitles', 'my_subtitles') ?></a>
        <a href="subtitles.php?action=new" class="brackets"><?= Lang::get('subtitles', 'bookmarked_subtitles') ?></a>
    </div>
    <div id="subtitle_search_box">
        <input id="subtitle_search_title" type="search" placeholder="<?= Lang::get('subtitles', 'title_or_imdb_link') ?>">
        <input id="subtitle_search_year" type="search" placeholder="<?= Lang::get('subtitles', 'year_optional') ?>">
        <select id="subtitle_search_language" name="TargetLanguageId" class="form__input">
            <!-- 请注意，下列 option 的 value 都与 GPW 不对应 -->
            <option value="14">简中</option>
            <option value="14">繁中</option>
            <option value="3">English</option>
            <option value="14">日语 japanese</option>
            <option value="19">韩语 korean</option>
            <option value="" selected="selected">---</option>
            <option value="22">Arabic</option>
            <option value="49">Brazilian Portuguese</option>
            <option value="29">Bulgarian</option>
            <option value="14">Chinese</option>
            <option value="23">Croatian</option>
            <option value="30">Czech</option>
            <option value="10">Danish</option>
            <option value="9">Dutch</option>
            <option value="38">Estonian</option>
            <option value="15">Finnish</option>
            <option value="6">German</option>
            <option value="26">Greek</option>
            <option value="40">Hebrew</option>
            <option value="41">Hindi</option>
            <option value="24">Hungarian</option>
            <option value="28">Icelandic</option>
            <option value="47">Indonesian</option>
            <option value="16">Italian</option>
            </option>
            <option value="37">Latvian</option>
            <option value="39">Lithuanian</option>
            <option value="12">Norwegian</option>
            <option value="52">Persian</option>
            <option value="17">Polish</option>
            <option value="21">Portuguese</option>
            <option value="13">Romanian</option>
            <option value="7">Russian</option>
            <option value="31">Serbian</option>
            <option value="42">Slovak</option>
            <option value="43">Slovenian</option>
            <option value="11">Swedish</option>
            <option value="20">Thai</option>
            <option value="18">Turkish</option>
            <option value="34">Ukrainian</option>
            <option value="25">Vietnamese</option>
        </select>
        <button><?= Lang::get('subtitles', 'search') ?></button>
    </div>
    <div id="subtitle_browser">
        <div class="thead subtitle_language"><?= Lang::get('global', 'language') ?></div>
        <div class="thead movie_title"><?= Lang::get('subtitles', 'movie_title') ?></div>
        <div class="thead subtitle_language"><?= Lang::get('global', 'language') ?></div>
        <div class="thead movie_title"><?= Lang::get('subtitles', 'movie_title') ?></div>
        <!-- 如果是多语字幕，下边就亮联合国旗 -->
        <div class="tbody subtitle_language">国旗</div>
        <div class="tbody movie_title"><a href="subtitles.php?action=detail">[电影中文名] 电影英文名 (年) by 导演名</a><span class="float_right">[ <a>DL</a> ]</span></div>
        <div class="tbody subtitle_language">国旗</div>
        <div class="tbody movie_title"><a href="subtitles.php?action=detail">[电影中文名] 电影英文名 (年) by 导演名</a><span class="float_right">[ <a>DL</a> ]</span></div>
    </div>
</div>



<?
View::show_footer();
