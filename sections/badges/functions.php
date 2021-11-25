<?

//总统计下载量（G）
function badge_downloaded($Count) {
    $Size = $Count * 1024 * 1024 * 1024;
    G::$DB->query(
        "SELECT ID 
        from users_main um 
        where `Downloaded` + (select sum(`Downloaded`) from users_freeleeches uf where um.ID = uf.UserID) >= $Size"
    );
    return G::$DB->collect("ID");
}

function badge_count_downloaded($UserID) {
    G::$DB->query(
        "SELECT (`Downloaded` + (select sum(`Downloaded`) from users_freeleeches uf where um.ID = uf.UserID)) / 1024 / 1024 / 1024
        from users_main um 
        where id = $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//总统计上传量（G）
function badge_uploaded($Count) {
    $Size = $Count * 1024 * 1024 * 1024;
    G::$DB->query(
        "SELECT ID 
        from users_main um 
        where `Uploaded` >= $Size"
    );
    return G::$DB->collect("ID");
}

function badge_count_uploaded($UserID) {
    G::$DB->query(
        "SELECT `Uploaded` / 1024 / 1024 / 1024
        from users_main um 
        where id = $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//总上传数
function badge_uploaded_count($Count) {
    G::$DB->query(
        "SELECT `UserID` 
        FROM `torrents` 
        GROUP BY `UserID` 
        HAVING count(`ID`) >= $Count"
    );
    return G::$DB->collect("UserID");
}

function badge_count_uploaded_count($UserID) {
    G::$DB->query(
        "SELECT count(`ID`)
        from `torrents` 
        where UserID = $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//flac CD 上传数
function badge_flac_cd_u_cnt($Count) {
    G::$DB->query(
        "SELECT `UserID` 
        FROM `torrents`
        where Media = 'CD' and format = 'FLAC'
        GROUP BY `UserID` 
        HAVING count(`ID`) >= $Count"
    );
    return G::$DB->collect("UserID");
}

function badge_count_flac_cd_u_cnt($UserID) {
    G::$DB->query(
        "SELECT count(`ID`)
        from `torrents` 
        where UserID = $UserID and Media = 'CD' and format = 'FLAC'"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//flac Web 上传数
function badge_flac_web_u_cnt($Count) {
    G::$DB->query(
        "SELECT `UserID` 
        FROM `torrents`
        where Media = 'WEB' and format = 'FLAC'
        GROUP BY `UserID` 
        HAVING count(`ID`) >= $Count"
    );
    return G::$DB->collect("UserID");
}

function badge_count_flac_web_u_cnt($UserID) {
    G::$DB->query(
        "SELECT count(`ID`)
        from `torrents` 
        where UserID = $UserID and Media = 'WEB' and format = 'FLAC'"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//flac 黑胶/SACD 上传数
function badge_flac_sacd_u_cnt($Count) {
    G::$DB->query(
        "SELECT `UserID` 
        FROM `torrents`
        where Media in ('Vinyl', 'SACD') and format = 'FLAC'
        GROUP BY `UserID` 
        HAVING count(`ID`) >= $Count"
    );
    return G::$DB->collect("UserID");
}

function badge_count_flac_sacd_u_cnt($UserID) {
    G::$DB->query(
        "SELECT count(`ID`)
        from `torrents` 
        where UserID = $UserID and Media in ('Vinyl', 'SACD') and format = 'FLAC'"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//flac DVD/磁带/soundbord/DAT/Blu-Ray上传数
function badge_flac_dvd_u_cnt($Count) {
    G::$DB->query(
        "SELECT `UserID` 
        FROM `torrents`
        where Media in ('DVD', 'Cassette', 'Soundbord', 'DAT', 'Blu-ray') and format = 'FLAC'
        GROUP BY `UserID` 
        HAVING count(`ID`) >= $Count"
    );
    return G::$DB->collect("UserID");
}

function badge_count_flac_dvd_u_cnt($UserID) {
    G::$DB->query(
        "SELECT count(`ID`)
        from `torrents` 
        where UserID = $UserID and Media in ('DVD', 'Cassette', 'Soundbord', 'DAT', 'Blu-ray') and format = 'FLAC'"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//其他 所有媒介
function badge_mp3_aac_u_cnt($Count) {
    G::$DB->query(
        "SELECT `UserID` 
        FROM `torrents`
        where format in ('MP3', 'AAC')
        GROUP BY `UserID` 
        HAVING count(`ID`) >= $Count"
    );
    return G::$DB->collect("UserID");
}

function badge_count_mp3_aac_u_cnt($UserID) {
    G::$DB->query(
        "SELECT count(`ID`)
        from `torrents` 
        where UserID = $UserID and format in ('MP3', 'AAC')"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//总下载数（100M单种）
function badge_downloaded_count($Count) {
    $Size = $Count * 100 * 1024 * 1024;
    /*
    SELECT uid
    FROM `xbt_snatched`
    GROUP BY `uid`
    HAVING COUNT(DISTINCT x.fid) >= $Count AND
    (SELECT `Downloaded` + (select sum(`Downloaded`) from users_freeleeches uf where um.ID = uf.UserID)
        from users_main um
        where id = uid) >= $Size
    */
    G::$DB->query(
        "SELECT uid ID
        FROM `xbt_snatched`
        GROUP BY `uid` 
        HAVING COUNT(DISTINCT fid) >= $Count AND 
        (   SELECT `Downloaded` + (
                select sum(`Downloaded`) 
                from users_freeleeches uf 
                where um.ID = uf.UserID
                ) 
            from users_main um 
            where id = uid
        ) >= $Size"
    );
    return G::$DB->collect("ID");
}

function badge_count_downloaded_count($UserID) {
    G::$DB->query(
        "SELECT least(
            COUNT(DISTINCT fid),
            (SELECT `Downloaded` + (select sum(`Downloaded`) from users_freeleeches uf where um.ID = uf.UserID) 
             from users_main um where um.id = uid) / (100 * 1024 * 1024)
            )
        from xbt_snatched
        where uid = $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//flac CD 下载数（120M单种）
function badge_flac_cd_d_cnt($Count) {
    $Size = $Count * 120 * 1024 * 1024;
    G::$DB->query(
        "SELECT uid ID from xbt_snatched LEFT join torrents on fid=id where format='FLAC' and media='CD' GROUP by uid having count(fid)>= $Count and sum(size)>= $Size"
    );
    return G::$DB->collect("ID");
}

function badge_count_flac_cd_d_cnt($UserID) {
    G::$DB->query(
        "SELECT least(count(fid), sum(size)/(120*1024*1024)) from xbt_snatched LEFT join torrents on fid=id where format='FLAC' and media='CD' and uid= $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//flac Web下载数（120M单种）
function badge_flac_web_d_cnt($Count) {
    $Size = $Count * 120 * 1024 * 1024;
    G::$DB->query(
        "SELECT uid ID from xbt_snatched LEFT join torrents on fid=id where format='FLAC' and media='WEB' GROUP by uid having count(fid)>= $Count and sum(size)>= $Size"
    );
    return G::$DB->collect("ID");
}

function badge_count_flac_web_d_cnt($UserID) {
    G::$DB->query(
        "SELECT least(count(fid), sum(size)/(120*1024*1024)) from xbt_snatched LEFT join torrents on fid=id where format='FLAC' and media='WEB' and uid= $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//flac 黑胶/SACD 下载数（120M单种）
function badge_flac_sacd_d_cnt($Count) {
    $Size = $Count * 120 * 1024 * 1024;
    G::$DB->query(
        "SELECT uid ID from xbt_snatched LEFT join torrents on fid=id where format='FLAC' and Media in ('Vinyl', 'SACD') GROUP by uid having count(fid)>= $Count and sum(size)>= $Size"
    );
    return G::$DB->collect("ID");
}

function badge_count_flac_sacd_d_cnt($UserID) {
    G::$DB->query(
        "SELECT least(count(fid), sum(size)/(120*1024*1024)) from xbt_snatched LEFT join torrents on fid=id where format='FLAC' and Media in ('Vinyl', 'SACD') and uid= $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//flac DVD/磁带/soundbord/DAT/Blu-Ray 下载数（120M单种）
function badge_flac_dvd_d_cnt($Count) {
    $Size = $Count * 120 * 1024 * 1024;
    G::$DB->query(
        "SELECT uid ID from xbt_snatched LEFT join torrents on fid=id where format='FLAC' and Media in ('DVD', 'Cassette', 'Soundbord', 'DAT', 'Blu-ray') GROUP by uid having count(fid) >= $Count and sum(size)>= $Size"
    );
    return G::$DB->collect("ID");
}

function badge_count_flac_dvd_d_cnt($UserID) {
    G::$DB->query(
        "SELECT least(count(fid), sum(size)/(120*1024*1024)) from xbt_snatched LEFT join torrents on fid=id where format='FLAC' and Media in ('DVD', 'Cassette', 'Soundbord', 'DAT', 'Blu-ray') and uid= $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//其他 所有媒介（40M单种）
function badge_mp3_aac_d_cnt($Count) {
    $Size = $Count * 40 * 1024 * 1024;
    G::$DB->query(
        "SELECT uid ID from xbt_snatched LEFT join torrents on fid=id where format in ('MP3', 'AAC') GROUP by uid having count(fid)>= $Count and sum(size)>= $Size"
    );
    return G::$DB->collect("ID");
}

function badge_count_mp3_aac_d_cnt($UserID) {
    G::$DB->query(
        "SELECT least(count(fid), sum(size)/(40*1024*1024)) from xbt_snatched LEFT join torrents on fid=id where format in ('MP3', 'AAC') and uid= $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

//站内论坛帖子数（包括发帖）
function badge_post_thread_count($Count) {
    G::$DB->query(
        "SELECT userid ID from (SELECT fp.id id, fp.authorid userid from forums_posts fp LEFT join forums_topics ft on fp.topicid=ft.id where ForumID != 34
        UNION select id id, authorid userid from forums_topics where ForumID != 34) tp group by userid HAVING count(id) >= $Count"
    );
    return G::$DB->collect("ID");
}

function badge_count_post_thread_count($UserID) {
    G::$DB->query(
        "SELECT count(id) from (SELECT fp.id id, fp.authorid userid from forums_posts fp LEFT join forums_topics ft on fp.topicid=ft.id where ForumID != 34 and fp.authorid = $UserID UNION select id id, authorid userid from forums_topics ft where ForumID != 34 and ft.authorid = $UserID) tp"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}

function badge_donor($Count) {
    G::$DB->query(
        "SELECT UserID ID from users_donor_ranks where TotalRank >= $Count"
    );
    return G::$DB->collect("ID");
}

function badge_count_donor($UserID) {
    G::$DB->query(
        "SELECT TotalRank from users_donor_ranks where UserID = $UserID"
    );
    list($Count) = G::$DB->next_record();
    return $Count;
}
