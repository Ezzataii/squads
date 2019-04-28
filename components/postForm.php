<div class="dropdown show" style="margin-bottom: 30px;">
  <button class="btn btn-primary dropdown-toggle btn-block" id="postDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Post
  </button>
  <div class="dropdown-menu" aria-labelledby="postDropdownMenuLink">
    <a class="dropdown-item" type="button" data-toggle="modal" data-target="#modalTextPostForm">Text</a>
    <a class="dropdown-item" type="button" data-toggle="modal" data-target="#modalImagePostForm">Image</a>
    <a class="dropdown-item" type="button" data-toggle="modal" data-target="#modalVideoPostForm">Video</a>
  </div>
</div>




<div id="modalTextPostForm" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Text Post</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <?php include("postFormComponents/textPostForm.php") ?>
      </div>
    </div>
  </div>
</div>


<div id="modalImagePostForm" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Image Post</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <?php include("postFormComponents/imagePostForm.php") ?>
      </div>
    </div>
  </div>
</div>


<div id="modalVideoPostForm" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Video Post</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <?php include("postFormComponents/videoPostForm.php") ?>
      </div>
    </div>
  </div>
</div>