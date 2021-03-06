@extends('admin/default')

@section('button')
   <a type="button" href="{{ route('admin.banner.create') }}" class="btn btn-sm btn-outline-primary ml-3 pr-3 pl-3 add-programme">Add Banner</a>
@endsection

@section('content')
   <!-- Main content -->
   <section class="content">
	  <div class="container-fluid">
		 <!-- Small boxes (Stat box) -->
		 <div class="row">
			<div class="col-12 col-sm-12 col-md-12 col-lg-12">
				@if (session('success'))
		            <div class="alert alert-success alert-dismissible">
		               <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		               <h5><i class="icon fas fa-check"></i> Success!</h5>
		               <p> {{ session('success') }} </p>
		            </div>
		        @endif
		        @if (session('fail'))
		            <div class="alert alert-danger alert-dismissible">
		               <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		               <h5><i class="icon fas fa-check"></i> Danger!</h5>
		               <p> {{ session('fail') }} </p>
		            </div>
		        @endif
			   <div class="card card-primary card-outline card-outline-tabs">
				  <div class="card-body">
					<table class="table table-hover text-nowrap my-datatable">
						<thead>
							<tr>
								<th>ID</th>
								<th>Image</th>
								<th>Title</th>
								<th>Title Hindi</th>
								<th>Description</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="banner_panel">
							@php
								$counter = 1;
							@endphp
							@foreach($banners as $banner)
									<tr>
										<td>{{ $counter }}</td>
										<td>
											<a href="{{ ($banner->feature_img) ? $banner->feature_img : '' }}" data-toggle="lightbox">
												<img src="{{ $banner->feature_img }}" width="75" height="75">
											</a>
										</td>
										<td>{{ $banner->title }}</td>
										<td>{{ $banner->title_hindi }}</td>
										<td>{{ substr($banner->description, 0,50) }}...</td>
										<td><?= ($banner->status == 0) ? "<span class='badge bg-danger'>Inactive</span>" : "<span class='badge bg-success'>Active</span>"; ?></td>
										<td>
											<a class="btn btn-xs btn-info" href="{{ route('admin.banner.show', $banner) }}" role="button" title="View"><i class="fas fa-eye"></i></a>
											<a class="btn btn-xs btn-success" href="{{ route('admin.banner.edit', $banner) }}" role="button" title="Edit"><i class="fas fa-pencil-alt"></i></a>
											<a class="btn btn-xs btn-danger ajax-delete" href="{{ route('admin.banner.delete', $banner) }}" role="button" title="Danger" data-programme_id="{{$banner->id}}" ><i class="fas fa-trash"></i></a>
										</td>
									</tr>
								@php
									$counter++;
								@endphp
							@endforeach
						</tbody>
					</table>
				  </div>
				  <!-- /.card -->
			   </div>
			</div>            
		 </div>
		 <!-- /.row -->
	  </div>
	  <!-- /.container-fluid -->
   </section>
   <!-- /.content -->
@endsection

@section('js')
<script type="text/javascript">
   
</script>
<script>
   $(function () {
	  $('.my-datatable').DataTable({
		 "paging": true,
		 "lengthChange": false,
		 "searching": false,
		 "ordering": true,
		 "info": true,
		 "autoWidth": false,
		 "responsive": true,
	  });
	  	$(document).on('click', '[data-toggle="lightbox"]', function(event) {
        	event.preventDefault();
        	$(this).ekkoLightbox();
      	});
   });

	var slider = (function () {

	var url = "{{url('/')}}"+"/admin/sliders/";

	var swalTitle = 'Really destroy this ?';
	var confirmButtonText = 'Yes';
	var cancelButtonText = 'No';
	var errorAjax = "Something went wrong";
	var title = "Add New";
	var onReady = function () {
		$('#banner_panel').on('click', 'td a.ajax-edit', function (event) {
			event.preventDefault();
			var that  = $(this);
			var url   = that.attr('href');
			var title = "Programme Edit";
			sbm.loadMultiPartForm(url, $(this), errorAjax, title);
		})
		.on('click', 'td a.ajax-view', function (event) {
			event.preventDefault();
			var that  = $(this);
			// console.log(that); return false;

			var url   = that.attr('href');
			var title = "Programme View";
			sbm.loadView(title, event, $(this), url)
		})
		.on('click', 'td a.ajax-delete', function (event) {
			event.preventDefault();
			var url   = $(this).attr('href');
			// console.log(url); return false;
			sbm.destroy(event, $(this), url, swalTitle, confirmButtonText, cancelButtonText, errorAjax)
		});
	

		// add new Slider
		$(document).on('click', '.add-slider', function () {
			var my_url = url + 'create';
			sbm.loadMultiPartForm(my_url, $(this), errorAjax, title);
		});
	}

return {
	onReady: onReady
}

})()

$(document).ready(slider.onReady)

</script>
@endsection