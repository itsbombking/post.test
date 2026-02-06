<table id="table-product" class="table table-bordered table-striped">
	<thead>
	<tr>
		<th>No</th>
		<th>Nama Produk</th>
		<th>Kategori</th>
		<th>Harga</th>
<!--		<th>Status</th>-->
		<th>Action</th>
	</tr>
	</thead>
	<tbody>
	<?php
	if ($products) {
		$no = 1;
		foreach ($products as $t) {
			?>
			<tr>
				<td><?php echo $no++;?></td>
				<td><?php echo $t->nama_produk;?></td>
				<td><?php echo $t->nama_kategori;?></td>
				<td data-search="<?php echo $t->harga; ?>" data-order="<?php echo $t->harga; ?>" ><span class="pull-right"><?php echo $t->harga_format;?></span></td>
<!--				<td>--><?php //echo $t->nama_status;?><!--</td>-->
				<td>
					<p>
						<!-- <button type="button" class="btn bg-purple margin">.btn.bg-purple</button>
						<button type="button" class="btn bg-navy margin">.btn.bg-navy</button> -->
						<a href="javascript:void(0)" data-href="<?php echo site_url('pos/product/update/'.$t->id_produk);?>" class="btn btn-sm bg-yellow update ml-3" title="Ubah"><i class="fa fa-pencil "></i></a>
						<a href="javascript:void(0)" data-product-name="<?php echo $t->text ?>" data-href="<?php echo site_url('pos/product/dodelete/'.$t->id_produk);?>" class="btn btn-sm bg-red delete ml-3" title="Hapus"><i class="fa fa-trash"></i></a>
					</p>
				</td>
			</tr>
			<?php
		}
	}
	?>
	</tbody>
</table>
<div class="modal fade" id="modify-data" data-backdrop="static" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true"></div>

<script>
	function loadmodal(url) {
		$('div#modify-data')
			.empty()
			.load(url, function (response, status, xhr) {
				if (status === 'error') {
					Swal.mixin({
						customClass: {
							confirmButton: 'btn btn-sm btn-success m-1',
							cancelButton: 'btn btn-sm btn-warning m-1',
							denyButton: 'btn btn-sm btn-danger m-1',
						},
						buttonsStyling: false,
					}).fire({
						position: 'top',
						icon: 'error',
						title: 'Terjadi Kesalahan',
						html: (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText),
						showCloseButton: true,
						showConfirmButton: false,
						showDenyButton: true,
						denyButtonText: `Tutup`,
					}).then(function () {
					});
				} else {
					Swal.close()
					$('div#modify-data').modal('show');
				}
			});
	}
	$(document).ready(function(){
		$('table#table-product').DataTable();

		$('#table-product').on('click', 'a.update', function () {
			var row = $(this)
			loadmodal(row.data('href'))
		})
		$('#table-product').on('click', 'a.delete', function () {
			var row = $(this)
			var productName = row.data('product-name');
			Swal.mixin({
				customClass: {
					confirmButton: 'btn btn-sm btn-success ml-3',
					cancelButton: 'btn btn-sm btn-warning ml-3',
					denyButton: 'btn btn-sm btn-danger ml-3',
				},
				buttonsStyling: false,
			}).fire({
				title: 'Konfirmasi Hapus',
				html: 'Konfirmasi hapus data <b>'+ productName +'</b> ?',
				icon: 'question',
				showCloseButton: true,
				showDenyButton: true,
				confirmButtonText: 'Konfirmasi',
				denyButtonText: 'Batal',
			}).then(function (result) {
				if (result.isConfirmed) {
					$.ajax({
						url: row.data('href'),
						success: function (data) {
							Swal.mixin({
								customClass: {
									confirmButton: 'btn btn-sm ml-3',
									cancelButton: 'btn btn-sm ml-3',
									denyButton: 'btn btn-sm ml-3',
								},
								buttonsStyling: false,
							}).fire({
								position: 'top',
								icon: 'success',
								title: 'Berhasil',
								html: data.message,
								timer: 3000,
								timerProgressBar: true,
								showCloseButton: true,
								showConfirmButton: false,
								showDenyButton: true,
								denyButtonText: `Tutup`,
							}).then(function () {
								loadListData('.list-data', true);
							});
						},
						error: function (xhr, status, thrown) {
							console.log(xhr)
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
								title: 'Gagal Diubah',
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
			});
		})
	})
</script>
