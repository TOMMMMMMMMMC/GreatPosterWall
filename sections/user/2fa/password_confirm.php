<?
View::show_header('双重认证');
?>

<div class="box pad">
    <p>请注意，如果你丢失了你的双重认证密钥以及所有的备用密钥，即使是 <?= SITE_NAME ?> 的工作人员也救不回你的账号。请确保你将备用密钥安置在妥当之处。</p>
</div>

<form method="post">
    <table cellpadding="6" cellspacing="1" border="0" width="100%" class="layout border">
        <thead>
            <tr class="colhead_dark">
                <td colspan="2">
                    <strong>请输入密码以禁用双重认证。</strong>
                </td>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="label">
                    <label for="password"><strong>密码</strong></label>
                </td>

                <td>
                    <input type="password" size="50" name="password" id="password" />

                    <? if (isset($_GET['invalid'])) : ?>
                        <p class="warning">密码无效。</p>
                    <? endif; ?>
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