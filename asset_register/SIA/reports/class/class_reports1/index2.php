<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <button id="toggle-button" type="button" class="btn btn-secondary">Hide/Show Button</button>
        <button id="target-button" type="button" class="btn btn-primary">Target Button</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('toggle-button').addEventListener('click', function() {
    var targetButton = document.getElementById('target-button');
    if (targetButton.style.display === 'none') {
      targetButton.style.display = 'block';
    } else {
      targetButton.style.display = 'none';
    }
  });
</script>