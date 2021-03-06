<div class="row">
	<div class="col-12">

		<!-- /.card-header -->
		<!-- form start -->
		<form role="form" method="post" action="{{ route('admin.category.store') }}" id="add_new">
			@csrf
			<input type="hidden" name="parent" value="0">
			<div class="card-body">
				<div class="form-group">
					<label for="title">Title</label>
					<input type="text" class="form-control" id="title" name="title" placeholder="Enter title" value="{{ old('title') }}">
				</div>
				<div class="form-group">
					<label for="title_hindi">Title Hindi</label>
					<input type="text" class="form-control" id="title_hindi" name="title_hindi" placeholder="Enter Hindi title" value="{{ old('title_hindi') }}">
				</div>
				<div class="form-group">
					<label for="slug">Slug</label>
					<input type="text" class="form-control" id="slug" name="slug" placeholder="Slug" value="{{ old('slug') }}">
				</div>
				<!-- upload the icon -->
				<div class="form-group">
					<label for="icon">Icon Image</label><span class="text-danger">&#42;</span>
					<div class="input-group">
						<div class="custom-file">
						<input type="file" class="custom-file-input" id="icon" name="icon">
							<label class="custom-file-label" for="icon">Choose file</label>
						</div>
					</div>
					<p class="text-muted ml-1 mt-50"><small>Allowed JPG, GIF or PNG. Max size of 800kB</small></p>
				</div>
				<div class="form-group">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="status" name="status" value="1">
                      <label class="custom-control-label" for="status">status</label>
                    </div>
                </div>
			</div>
			<!-- /.card-body -->
		</form>
	</div>
</div>
@section('js')
<script src="{{ asset('plugins/voca/voca.min.js') }}"></script>
<script>

    $('#slug').keyup(function () {
        $(this).val(v.slugify($(this).val()))
    })

    $('#title').keyup(function () {
        $('#slug').val(v.slugify($(this).val()))
    })

</script>
@endsection