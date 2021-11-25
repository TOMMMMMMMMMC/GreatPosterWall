<script>
	var collages

	function search(s) {
		var title = $('#collage_title').val()
		$.ajax({
			url: "collages.php",
			data: {
				"action": "ajaxsearch",
				"name": title,
				"type": 'torrent',
				"self": s
			},
			type: "POST",
			success: data => {
				$("#select_collage").empty();
				collages = data
				for (var collageid in collages) {
					$("#select_collage").append('<option value="' + collageid + '">' + collages[collageid].name + '</option>')
				};
				selectCollage()
			},
			dataType: "json",
		})
	}

	function addCollage(groupid) {
		var collageid = $('#select_collage').val()
		if (collageid) {
			$.ajax({
				url: "collages.php",
				data: {
					"action": "add_torrent",
					"auth": "<?= $LoggedUser['AuthKey'] ?>",
					"collageid": collageid,
					"groupid": groupid
				},
				type: "POST",
				success: data => {
					if (data.state) {
						alert("<?= Lang::get('collages', 'collage_add_success') ?>")
					} else {
						alert("<?= Lang::get('collages', 'collage_add_error') ?>")
					}
				},
				dataType: "json",
			})
		}
	}

	function openCollage() {
		var collageid = $('#select_collage').val()
		if (collageid) {
			window.open("collages.php?id=" + collageid);
		}
	}

	function selectCollage() {
		var collageid = $('#select_collage').val()
		if (collageid) {
			$('#s_c_span_category')[0].innerHTML = '<a href="wiki.php?action=article&id=243" target="_blank">' + collages[collageid].category + '</a>'
			$('#s_c_span_author')[0].innerHTML = '<a href="user.php?id=' + collages[collageid].userid + '" target="_blank">' + collages[collageid].username + '</a>'
			$('#s_c_span_description')[0].innerHTML = collages[collageid].description
		} else {
			$('#s_c_span_category')[0].innerHTML = ''
			$('#s_c_span_author')[0].innerHTML = ''
			$('#s_c_span_description')[0].innerHTML = ''
		}
	}
	$(() => search(true))
</script>
<div class="caidan artbox">
	<div class="sec-title"><span><?= Lang::get('collages', 'add_to_collage') ?></span></div>
	<div class="body">
		<div id="SearchCollage">
			<input type="text" id="collage_title" name="title" placeholder="<?= Lang::get('collages', 'collage_search') ?>" />
			<input type="button" value="<?= Lang::get('collages', 'search') ?>" onclick="search(false)" />
		</div>
		<div id="AddCollage">
			<select id="select_collage" onchange="selectCollage()"></select>
			<div id="selected_collage_category">
				<span><?= Lang::get('collages', 'selected_collage_category') ?>: </span>
				<span id="s_c_span_category"></span>
			</div>
			<div id="selected_collage_author">
				<span><?= Lang::get('collages', 'selected_collage_author') ?>: </span>
				<span id="s_c_span_author"></span>
			</div>
			<div id="selected_collage_description">
				<span><?= Lang::get('collages', 'selected_collage_description') ?>: </span>
				<span id="s_c_span_description"></span>
			</div>
			<div class="center">
				<input type="button" value="<?= Lang::get('collages', 'open_collage') ?>" onclick="openCollage()" />
				<input type="button" value="<?= Lang::get('collages', 'add_to_collage') ?>" onclick="addCollage(<?= $GroupID ?>)" />
			</div>
		</div>
	</div>
</div>