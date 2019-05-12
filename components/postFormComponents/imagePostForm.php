<form id="imagePostForm">
  <div class="form-group">
    <label for="imagePostFormText">Text</label>
    <textarea class="form-control" id="imagePostFormText" rows="3"></textarea>
  </div>

  <div class="form-group">
    <label for="imagePostFormImage">Image</label>
    <div class="input-group mb-3" id="imagePostFormImage">
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="imagePostFileInput">
        <label class="custom-file-label" for="imagePostFileInput">Choose file</label>
      </div>
    </div>
  </div>

  <label class="radio-inline">
    <input type="radio" name="levelOfAccessRadioImagePost" value="public" checked>
    Public
  </label> &nbsp;
  <label class="radio-inline">
    <input type="radio" name="levelOfAccessRadioImagePost" value="friends-only">
    Friends Only
  </label> &nbsp;
  <label>
    <input type="radio" name="levelOfAccessRadioImagePost" value="private">
    Private
  </label>
  <br>

  <button type="submit" class="btn btn-primary btn-block">Post</button>

  <div id="imagePostFormStatus"></div>
</form>




<script>
$('#imagePostFileInput').change((e) => {
  var fileName = e.target.files[0].name;
  $(e.target).next('.custom-file-label').html(fileName);
});

$("#imagePostForm").submit((e) => {
  e.preventDefault();

  var formData = new FormData();
  formData.append("text", $("#imagePostFormText").val());
  formData.append("image", $("#imagePostFileInput").prop('files')[0]);
  formData.append("level-of-access", $(`input:radio[name="levelOfAccessRadioImagePost"]:checked`).val());

  $.ajax({
    type: 'POST',
    url: '../api/user-post.php/post/image?u=<?= $user ?>',
    data: formData,
    cache: false,
    contentType: false,
    processData: false,
    success: (res) => {
      $("#imagePostFormStatus").html(res);
      console.log(res);
      //location.reload();
    }
  });
})
</script>