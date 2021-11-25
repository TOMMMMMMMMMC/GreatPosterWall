<?
View::show_header(Lang::get('user', 'two_factor_authentication'));
?>

<div class="box pad">
    <p><?= Lang::get('user', 'two_factor_authentication_note_1') ?> <?= SITE_NAME ?> <?= Lang::get('user', 'two_factor_authentication_note_2') ?></p>
</div>

<form method="post">
    <table id="user_2fa_step2_table" class="layout border">
        <thead>
            <tr class="colhead_dark">
                <td colspan="2">
                    <strong><?= Lang::get('user', 'please_enter_2fa_key') ?></strong>
                </td>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="label tooltip_interactive" title="<?= Lang::get('user', 'authentication_key_title') ?>" data-title-plain="If all went to plan last step, your authentication app should've given you a code. Please enter that here.">
                    <label for="2fa"><strong><?= Lang::get('user', 'authentication_key') ?>:</strong></label>
                </td>

                <td>
                    <input type="text" size="50" name="2fa" id="2fa" />
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <input type="submit">
                </td>
            </tr>
        </tbody>
    </table>
</form>

<? View::show_footer(); ?>