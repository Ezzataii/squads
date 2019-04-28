<form id="videoPostForm">
  <div class="form-group">
    <label for="videoPostFormText">Text</label>
    <textarea class="form-control" id="videoPostFormText" rows="3"></textarea>
  </div>

  <div class="form-group">
    <label for="videoPostFormVideo">Video</label>
    <div class="input-group mb-3" id="videoPostFormVideo">
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="videoPostFileInput">
        <label class="custom-file-label" for="videoPostFileInput">Choose file</label>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary btn-block">Post</button>

  <div id="videoPostFormStatus"></div>
</form>




<script>
$('#videoPostFileInput').change((e) => {
  var fileName = e.target.files[0].name;
  $(e.target).next('.custom-file-label').html(fileName);
});

$("#videoPostForm").submit((e) => {
  e.preventDefault();

  var formData = new FormData();
  formData.append("text", $("#videoPostFormText").val());
  formData.append("video", $("#videoPostFileInput").prop('files')[0]);

  $.ajax({
    type: 'POST',
    url: '../api/user-post.php/post/video?u=<?= $user ?>',
    data: formData,
    cache: false,
    contentType: false,
    processData: false,
    success: (res) => {
      $("#videoPostFormStatus").html(res);
      location.reload();
    }
  });
})
</script>