<?

/********************************************************************************
 ************ Permissions form ********************** user.php and tools.php ****
 ********************************************************************************
 ** This function is used to create both the class permissions form, and the   **
 ** user custom permissions form.                                             **
 ********************************************************************************/

$PermissionsArray = array(
    'site_leech' => 'Can leech (Does this work?).',
    'site_upload' => 'Upload torrent access.',
    'site_vote' => 'Request vote access.',
    'site_submit_requests' => 'Request create access.',
    'site_advanced_search' => 'Advanced search access.',
    'site_top10' => 'Top 10 access.',
    'site_advanced_top10' => 'Advanced Top 10 access.',
    'site_album_votes' => 'Voting for favorite torrents.',
    'site_torrents_notify' => 'Notifications access.',
    'site_collages_create' => 'Collage create access.',
    'site_collages_manage' => 'Collage manage access.',
    'site_collages_delete' => 'Collage delete access.',
    'site_collages_subscribe' => 'Collage subscription access.',
    'site_collages_personal' => 'Can have a personal collage.',
    'site_collages_renamepersonal' => 'Can rename own personal collages.',
    'site_make_bookmarks' => 'Bookmarks access.',
    'site_edit_wiki' => 'Wiki edit access.',
    'site_can_invite_always' => 'Can invite past user limit.',
    'site_can_invite' => 'Can invite users',
    'site_send_unlimited_invites' => 'Unlimited invites.',
    'site_moderate_requests' => 'Request moderation access.',
    'site_delete_artist' => 'Can delete artists (must be able to delete torrents+requests).',
    'site_moderate_forums' => 'Forum moderation access.',
    'site_admin_forums' => 'Forum administrator access.',
    'site_view_flow' => 'Can view stats and data pools.',
    'site_view_full_log' => 'Can view old log entries.',
    'site_view_torrent_snatchlist' => 'Can view torrent snatch lists.',
    'site_recommend_own' => 'Can recommend own torrents.',
    'site_manage_recommendations' => 'Recommendations management access.',
    'site_delete_tag' => 'Can delete tags.',
    'site_disable_ip_history' => 'Disable IP history.',
    'show_admin_team' => 'Show admin team.',
    'show_staff_username' => 'Show staff username.',
    'zip_downloader' => 'Download multiple torrents at once.',
    'site_debug' => 'Developer access.',
    'site_proxy_images' => 'Image proxy & anti-canary.',
    'site_search_many' => 'Can go past low limit of search results.',
    'users_edit_usernames' => 'Can edit usernames.',
    'users_edit_ratio' => 'Can edit anyone\'s upload/download amounts.',
    'users_edit_own_ratio' => 'Can edit own upload/download amounts.',
    'users_edit_titles' => 'Can edit titles.',
    'users_edit_avatars' => 'Can edit avatars.',
    'users_edit_invites' => 'Can edit invite numbers and cancel sent invites.',
    'users_edit_watch_hours' => 'Can edit contrib watch hours.',
    'users_edit_reset_keys' => 'Can reset passkey/authkey.',
    'users_edit_profiles' => 'Can edit anyone\'s profile.',
    'users_view_friends' => 'Can view anyone\'s friends.',
    'users_reset_own_keys' => 'Can reset own passkey/authkey.',
    'users_edit_password' => 'Can change passwords.',
    'users_promote_below' => 'Can promote users to below current level.',
    'users_promote_to' => 'Can promote users up to current level.',
    'users_give_donor' => 'Can give donor access.',
    'users_warn' => 'Can warn users.',
    'users_disable_users' => 'Can disable users.',
    'users_disable_posts' => 'Can disable users\' posting privileges.',
    'users_disable_any' => 'Can disable any users\' rights.',
    'users_delete_users' => 'Can delete users.',
    'users_view_invites' => 'Can view who user has invited.',
    'users_view_seedleech' => 'Can view what a user is seeding or leeching.',
    'users_view_uploaded' => 'Can view a user\'s uploads, regardless of privacy level.',
    'users_view_keys' => 'Can view passkeys.',
    'users_view_ips' => 'Can view IP addresses.',
    'users_view_email' => 'Can view email addresses.',
    'users_invite_notes' => 'Can add a note when inviting someone.',
    'users_override_paranoia' => 'Can override paranoia.',
    'users_logout' => 'Can log users out (old?).',
    'users_make_invisible' => 'Can make users invisible.',
    'users_mod' => 'Basic moderator tools.',
    'torrents_edit' => 'Can edit any torrent.',
    'torrents_delete' => 'Can delete torrents.',
    'torrents_delete_fast' => 'Can delete more than 3 torrents at a time.',
    'torrents_freeleech' => 'Can make torrents freeleech.',
    'torrents_search_fast' => 'Rapid search (for scripts).',
    'torrents_hide_dnu' => 'Hide the Do Not Upload list by default.',
    'torrents_fix_ghosts' => 'Can fix "ghost" groups on artist pages.',
    'admin_manage_news' => 'Can manage site news.',
    'admin_manage_blog' => 'Can manage the site blog.',
    'admin_manage_polls' => 'Can manage polls.',
    'admin_manage_forums' => 'Can manage forums (add/edit/delete).',
    'admin_manage_fls' => 'Can manage First Line Support (FLS) crew.',
    'admin_manage_user_fls' => 'Can manage user FL tokens.',
    'admin_manage_applicants' => 'Can manage job roles and user applications.',
    'admin_send_bonus' => 'Can give points in the forum, but not deduct own points.',
    'admin_bp_history' => 'Can view bonus points spent by other users.',
    'admin_reports' => 'Can access reports system.',
    'admin_advanced_user_search' => 'Can access advanced user search.',
    'admin_create_users' => 'Can create users through an administrative form.',
    'admin_donor_log' => 'Can view the donor log.',
    'admin_manage_ipbans' => 'Can manage IP bans.',
    'admin_dnu' => 'Can manage do not upload list.',
    'admin_clear_cache' => 'Can clear cached.',
    'admin_whitelist' => 'Can manage the list of allowed clients.',
    'admin_manage_permissions' => 'Can edit permission classes/user permissions.',
    'admin_schedule' => 'Can run the site schedule.',
    'admin_login_watch' => 'Can manage login watch.',
    'admin_manage_wiki' => 'Can manage wiki access.',
    'admin_update_geoip' => 'Can update geoIP data.',
    'admin_interviewer' => 'Can manage user application.',
    'torrents_check' => 'Can check torrents.',
    'self_torrents_check' => 'Can check self torrents.',
    'torrents_check_log' => 'Can view check log.',
    'site_collages_recover' => 'Can recover \'deleted\' collages.',
    'torrents_add_artist' => 'Can add artists to any group.',
    'edit_unknowns' => 'Can edit unknown release information.',
    'forums_polls_create' => 'Can create polls in the forums.',
    'forums_polls_moderate' => 'Can feature and close polls.',
    'project_team' => 'Is part of the project team.',
    'torrents_edit_vanityhouse' => 'Can mark groups as part of Vanity House.',
    'artist_edit_vanityhouse' => 'Can mark artists as part of Vanity House.',
    'site_tag_aliases_read' => 'Can view the list of tag aliases.',
    'staff_award' => 'Can view award.',
    'users_view_disabled' => 'Can view disabled privilege.',
    'forums_see_hidden' => 'Can see hidden posts.',
    'admin_manage_badges' => 'Can manage badges.',
    'events_reward_tokens' => 'Events reward tokens.',
    'events_reward_bonus' => 'Events reward bonus.',
    'events_reward_invites' => 'Events reward invites.',
    'events_reward_badges' => 'Events reward badges.',
    'events_reward_history' => 'Events reward history.'
);

function permissions_form() {
?>
    <div class="permissions">
        <div class="permission_container">
            <table>
                <tr class="colhead">
                    <td><?=Lang::get('permissions', 'site')?></td>
                </tr>
                <tr>
                    <td>
                        <?
                        display_perm('site_leech');
                        display_perm('site_upload');
                        display_perm('site_vote');
                        display_perm('site_submit_requests');
                        display_perm('site_advanced_search');
                        display_perm('site_top10');
                        display_perm('site_torrents_notify');
                        display_perm('site_collages_create');
                        display_perm('site_collages_manage');
                        display_perm('site_collages_delete');
                        display_perm('site_collages_subscribe');
                        display_perm('site_collages_personal');
                        display_perm('site_collages_renamepersonal');
                        display_perm('site_advanced_top10');
                        display_perm('site_album_votes');
                        display_perm('site_make_bookmarks');
                        display_perm('site_edit_wiki');
                        display_perm('site_can_invite_always');
                        display_perm('site_can_invite');
                        display_perm('site_send_unlimited_invites');
                        display_perm('site_moderate_requests');
                        display_perm('site_delete_artist');
                        display_perm('forums_polls_create');
                        display_perm('forums_polls_moderate');
                        display_perm('site_moderate_forums');
                        display_perm('site_admin_forums');
                        display_perm('site_view_flow');
                        display_perm('site_view_full_log');
                        display_perm('site_view_torrent_snatchlist');
                        display_perm('site_recommend_own');
                        display_perm('site_manage_recommendations');
                        display_perm('site_delete_tag');
                        display_perm('site_disable_ip_history');
                        display_perm('zip_downloader');
                        display_perm('site_debug');
                        display_perm('site_proxy_images');
                        display_perm('site_search_many');
                        display_perm('site_collages_recover');
                        display_perm('project_team');
                        display_perm('site_tag_aliases_read');
                        display_perm('forums_see_hidden');
                        display_perm('show_admin_team');
                        display_perm('show_staff_username');
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="permission_container">
            <table>
                <tr class="colhead">
                    <td><?=Lang::get('permissions', 'users')?></td>
                </tr>
                <tr>
                    <td>
                        <?
                        display_perm('users_edit_usernames');
                        display_perm('users_edit_ratio');
                        display_perm('users_edit_own_ratio');
                        display_perm('users_edit_titles');
                        display_perm('users_edit_avatars');
                        display_perm('users_edit_invites');
                        display_perm('users_edit_watch_hours');
                        display_perm('users_edit_reset_keys');
                        display_perm('users_edit_profiles');
                        display_perm('users_view_friends');
                        display_perm('users_reset_own_keys');
                        display_perm('users_edit_password');
                        display_perm('users_promote_below');
                        display_perm('users_promote_to');
                        display_perm('users_give_donor');
                        display_perm('users_warn');
                        display_perm('users_disable_users');
                        display_perm('users_disable_posts');
                        display_perm('users_disable_any');
                        display_perm('users_delete_users');
                        display_perm('users_view_invites');
                        display_perm('users_view_seedleech');
                        display_perm('users_view_uploaded');
                        display_perm('users_view_keys');
                        display_perm('users_view_ips');
                        display_perm('users_view_email');
                        display_perm('users_invite_notes');
                        display_perm('users_override_paranoia');
                        display_perm('users_make_invisible');
                        display_perm('users_logout');
                        display_perm('users_mod');
                        display_perm('staff_award');
                        display_perm('users_view_disabled');
                        ?>
                        <?=Lang::get('permissions', 'only_applicable_to_lower_class')?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="permission_container">
            <table>
                <tr class="colhead">
                    <td><?=Lang::get('permissions', 'torrents')?></td>
                </tr>
                <tr>
                    <td>
                        <?
                        display_perm('torrents_edit');
                        display_perm('torrents_check');
                        display_perm('self_torrents_check');
                        display_perm('torrents_check_log');
                        display_perm('torrents_delete');
                        display_perm('torrents_delete_fast');
                        display_perm('torrents_freeleech');
                        display_perm('torrents_search_fast');
                        display_perm('torrents_add_artist');
                        display_perm('edit_unknowns');
                        display_perm('torrents_edit_vanityhouse');
                        display_perm('artist_edit_vanityhouse');
                        display_perm('torrents_hide_dnu');
                        display_perm('torrents_fix_ghosts');
                        display_perm('torrents_trumpable');
                        display_perm('torrents_slot_edit');
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="permission_container">
            <table>
                <tr class="colhead">
                    <td><?=Lang::get('permissions', 'administrative')?></td>
                </tr>
                <tr>
                    <td>
                        <?
                        display_perm('admin_manage_news');
                        display_perm('admin_manage_blog');
                        display_perm('admin_manage_polls');
                        display_perm('admin_manage_forums');
                        display_perm('admin_manage_fls');
                        display_perm('admin_manage_user_fls');
                        display_perm('admin_manage_badges');
                        display_perm('admin_manage_applicants');
                        display_perm('admin_send_bonus');
                        display_perm('admin_reports');
                        display_perm('admin_bp_history');
                        display_perm('admin_advanced_user_search');
                        display_perm('admin_create_users');
                        display_perm('admin_donor_log');
                        display_perm('admin_manage_stylesheets');
                        display_perm('admin_manage_ipbans');
                        display_perm('admin_dnu');
                        display_perm('admin_clear_cache');
                        display_perm('admin_whitelist');
                        display_perm('admin_manage_permissions');
                        display_perm('admin_schedule');
                        display_perm('admin_login_watch');
                        display_perm('admin_manage_wiki');
                        display_perm('admin_update_geoip');
                        display_perm('admin_interviewer');
                        display_perm('events_reward_tokens');
                        display_perm('events_reward_bonus');
                        display_perm('events_reward_invites');
                        display_perm('events_reward_badges');
                        display_perm('events_reward_history');
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="submit_container"><input type="submit" name="submit" value="Save Permission Class" /></div>
    </div>
<?
}
