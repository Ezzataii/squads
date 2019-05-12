<form id="textPostForm">
  <div class="form-group">
    <label for="textPostFormText">Text</label>
    <textarea class="form-control" id="textPostFormText" rows="3" name="Text"></textarea>
  </div>

  <label class="radio-inline">
    <input type="radio" name="levelOfAccessRadioTextPost" value="public" checked>
    Public
  </label> &nbsp;
  <label class="radio-inline">
    <input type="radio" name="levelOfAccessRadioTextPost" value="friends-only">
    Friends Only
  </label> &nbsp;
  <label>
    <input type="radio" name="levelOfAccessRadioTextPost" value="private">
    Private
  </label>
  <br>

  <button type="submit" class="btn btn-primary btn-block">Post</button>

  <div id="textPostFormStatus"></div>
</form>






<script>
  $("#textPostForm").submit((e) => {
    e.preventDefault();

    var formData = new FormData();
    formData.append("text", $("#textPostFormText").val());
    formData.append("level-of-access", $(`input:radio[name="levelOfAccessRadioTextPost"]:checked`).val());

    $.ajax({
      type: 'POST',
      url: '../api/user-post.php/post/text?u=<?= $user ?>',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: (res) => {
        $("#textPostFormStatus").html(res);
        location.reload();
      }
    });
  })
</script>