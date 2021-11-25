<?
View::show_header(Lang::get('upload', 'image_host'), 'img_upload');
?>
<div class="header">
    <h2><?= Lang::get('upload', 'image_host') ?></h2>
</div>
<script>
    function imgUpload(file = false) {
        UploadImage(file, url => {
            $("#image").val(url)
            $("#uploaded_img").attr("src", url);
        });
    }

    function copy() {
        if ($("#image").val()) {
            $("#image").select()
            document.execCommand("Copy");
            alert("<?= Lang::get('upload', 'copied') ?>")
        }
    }
</script>
<div id="image_uploader">
    <input type="text" placeholder="<?= Lang::get('upload', 'image_placeholder') ?>" id="image" name="image" size="60" ondrop="drop(event)" ondragover="allowDrop(event)" />
    <input type="button" onclick="imgUpload()" value="上传" accept="image/gif,image/jpeg,image/jpg,image/png,image/svg" />
    <input type="button" onclick="copy()" value="复制"> <span id="imgUploadPer"></span>
</div>
<div id="uploaded_img_container">
    <img id="uploaded_img">
</div>
<?
View::show_footer();
