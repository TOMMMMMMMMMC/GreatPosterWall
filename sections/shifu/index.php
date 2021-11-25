<?
View::show_header(Lang::get('shifu', 'header_apprentice_wanted'), 'apply');
?>

<div class="thin">
    <div class="header">
        <h2><?= Lang::get('shifu', 'header_apprentice_wanted') ?></h2>
    </div>
    <div class="linkbox">
        <a href="/shifu.php?" class="brackets"><?= Lang::get('shifu', 'shifu_wanted') ?></a>
        <a href="/shifu.php?" class="brackets"><?= Lang::get('shifu', 'apprentice_wanted') ?></a>
        <a href="/shifu.php?" class="brackets"><?= Lang::get('shifu', 'information_card') ?></a>
        <a href="/shifu.php?" class="brackets"><?= Lang::get('shifu', 'relationship_management') ?></a>
    </div>
    <div class="box" id="info_container">
        <div class="info_card">
            <div class="username">
                <span>等级</span>
                <strong><a href="/user.php">Username</a></strong>
                <span>(5)</span>
            </div>
            <div class="avatar_container">
                <img class="avatar" src="https://pic.xiami.net/images/album/img32/22/5e9eeb2b5f387_1148132_1587473195.jpg?x-oss-process=image/quality,q_80/format,jpg">
            </div>
            <div class="introduction">
                <p>自我介绍和收徒要求</p>
            </div>
        </div>
    </div>
</div>

<?
View::show_footer();
