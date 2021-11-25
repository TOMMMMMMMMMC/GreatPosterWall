function retrieveNewURL(file, cb) {
  fetch(`/ajax.php?action=presigned_url&name=${file.name}`).then((response) => {
    response.text().then((data) => {
      var json = JSON.parse(data)
      if (json['msg']) {
        console.error(json['msg']);
      }
      cb(file, json['url'], json['name']);
    });
  }).catch((e) => {
    console.error(e);
  });
}

function UploadImage(file, after = (url) => { }) {
  var input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/gif,image/jpeg,image/jpg,image/png,image/svg'
  function up(f) {
    retrieveNewURL(f, (f, url, name) => {
      // 上传文件到服务器
      fetch(url, {
        method: 'PUT',
        body: f
      }).then(() => {
        // If multiple files are uploaded, append upload status on the next line.
        after(name);
      }).catch((e) => {
        console.error(e);
      });
    });
  }
  if (file) {
    up(file)
  } else {
    input.onchange = function () {
      file = input.files[0]
      up(file)
    }
    input.click()
  }
}

function allowDrop(ev) {
  ev.preventDefault()
}
function drop(event) {
  event.preventDefault()
  if (event.dataTransfer.files.length) {
    var file = event.dataTransfer.files[0]
    if (/image\/\w+/.test(file.type)) {
      UploadImage(file)
    }
  }
}

window.UploadImage = UploadImage
