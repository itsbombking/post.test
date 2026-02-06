<style>
	.ml-3{
		margin-left: 3px;
	}
</style>
<section class="content-header">
    <h1>
        Products
        <small>List of products</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <!-- <li><a href="#">Tables</a></li> -->
        <li class="active">Products</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
<div class="row">
<div class="col-xs-12">
<div class="box">
<div class="box-header">
    <h3 class="box-title"></h3>
	<button type="button" class="btn btn-sm btn-primary pull-right syncronize-data" data-href="<?php echo $syncUrl ?>" title="Singkronisasi Data"><i class="fa fa-cloud-download"></i></button>
	<button type="button" class="btn btn-sm btn-info pull-left reload-data" title="Muat Ulang Data"><i class="fa fa-refresh"></i></button>
</div>
<!-- /.box-header -->
<div class="box-body">
	<div class="list-data" data-href="<?php echo $listDataUrl ?>"></div>
</div>
<!-- /.box-body -->
</div>
<!-- /.box -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->

<script>


	function loadListData(selector = '.list-data', forceReload = false) {

		$(selector).each(function () {
			let container = $(this);
			let url = container.data('href');

			if (!url) return;

			// cegah double request bersamaan
			if (container.data('loading')) return;

			// skip jika sudah loaded dan tidak dipaksa reload
			if (!forceReload && container.data('loaded')) return;

			container.data('loading', true);

			// tampilkan spinner
			container.html(loadingSpinner());

			$.ajax({
				url: url,
				type: 'GET',
				success: function (response) {
					container.html(response);
					container.data('loaded', true);
				},
				error: function () {
					container.html('<div class="text-danger p-2">Failed to load data</div>');
				},
				complete: function () {
					container.data('loading', false);
				}
			});
		});

	}
	function loadingSpinner() {
		return `
        <div class="text-center p-3">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <div>Loading...</div>
        </div>
    `;
	}

	$(document).ready(function () {
		loadListData();
		$('button.reload-data').click(function () {
			loadListData('.list-data', true);
		})
		$('button.syncronize-data').click(function () {
			var row = $(this);
			Swal.mixin({
				customClass: {
					confirmButton: 'btn btn-sm btn-success ml-3',
					cancelButton: 'btn btn-sm btn-warning ml-3',
					denyButton: 'btn btn-sm btn-danger ml-3',
				},
				buttonsStyling: false,
			}).fire({
				position: 'top',
				icon: 'question',
				title: 'Perhatian',
				html: 'Apakah anda yakin ingin singkronisasi data dengan server pusat?',
				showCloseButton: true,
				showConfirmButton: true,
				showDenyButton: true,
				confirmButtonText: 'Konfirmasi',
				denyButtonText: 'Batal',
			}).then(function (result) {
				if (result.isConfirmed) {
					$.ajax({
						url: row.data('href'),
						type: 'GET',
						success: function (data) {

							Swal.mixin({
								customClass: {
									confirmButton: 'btn btn-sm btn-success ml-3',
									cancelButton: 'btn btn-sm btn-warning ml-3',
									denyButton: 'btn btn-sm btn-danger ml-3',
								},
								buttonsStyling: false,
							}).fire({
								position: 'top',
								icon: 'success',
								title: data.statusText,
								html: data.message,
								timer: 3000,
								timerProgressBar: true,
								showCloseButton: true,
								showConfirmButton: true,
								confirmButtonText: `Tutup`,
							}).then(function () {
								loadListData('.list-data', true);
							});
						},
						error: function (xhr, status, thrown) {
							Swal.mixin({
								customClass: {
									confirmButton: 'btn btn-sm btn-success ml-3',
									cancelButton: 'btn btn-sm btn-warning ml-3',
									denyButton: 'btn btn-sm btn-danger ml-3',
								},
								buttonsStyling: false,
							}).fire({
								position: 'top',
								icon: 'error',
								title: 'Gagal simpan',
								html: (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText),
								showCloseButton: true,
								showConfirmButton: false,
								showDenyButton: true,
								denyButtonText: `Tutup`,
							}).then(function () {
							});
						},
					});
				}
			})
		})
	})
</script>
