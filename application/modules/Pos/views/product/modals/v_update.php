<?php
?>

<div class="modal-dialog modal-md">
    <div class="modal-content">
        <form role="form" class="updateproduct" id="updateproduct" action="<?php echo $formAction ?>" method="post">
            <div class="modal-header">
                <h3 class="modal-title"><?php echo $modalTitle ?></h3>
            </div>
            <div class="modal-body">
				<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="form-group row">
                    <span class="col-sm-4 text-bold">Nama Produk</span>
                    <div class="col-sm-8">
                        <input type="text" name="product_name" class="form-control text-uppercase "
                               value="<?php echo $transaction->text ?>"/>
                    </div>
                </div>
                <div class="form-group row">
                    <span class="col-sm-4 text-bold">Harga</span>
                    <div class="col-sm-8">
                        <input type="text" name="price" class="form-control text-uppercase "
                               value="<?php echo $transaction->harga ?>"/>
                    </div>
                </div>
                <div class="form-group row">
                    <span class="col-sm-4 text-bold">Kategori</span>
                    <div class="col-sm-8">
                        <select name="category_id" class="form-control" id="category_id">
                            <?php foreach ($categoryData as $index => $item) { ?>
								<option <?php echo ($transaction->kategori_id == $item->id ? 'selected' : '' ); ?> value="<?php echo $item->id ?>"> <?php echo $item->text ?> </option>
							<?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <span class="col-sm-4 text-bold">Status</span>
                    <div class="col-sm-8">
						<select name="status_id" class="form-control" id="status_id">
							<?php foreach ($statusData as $index => $item) { ?>
								<option <?php echo ($transaction->status_id == $item->id ? 'selected' : '' ); ?> value="<?php echo $item->id ?>"> <?php echo $item->text ?> </option>
							<?php } ?>
						</select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md btn-warning mr-5" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-md btn-success mr-5" >Simpan</button>
            </div>
        </form>

    </div>
</div>
<script>
    $(document).ready(function () {
        $.extend($.validator.messages, {
            required: 'Bagian ini diperlukan...',
            remote: 'Harap perbaiki bidang ini...',
            email: 'Harap masukkan email yang valid...',
            url: 'Harap masukkan URL yang valid...',
            date: 'Harap masukkan tanggal yang valid...',
            dateISO: 'Harap masukkan tanggal yang valid (ISO)...',
            birthdate: 'Harap masukkan tanggal lahir tidak lebih dari 120 tahun...',
            time: 'Harap masukkan waktu yang valid...',
            number: 'Harap masukkan nomor valid...',
            digits: 'Harap masukkan hanya digit angka...',
            creditcard: 'Harap masukkan nomor kartu kredit yang benar...',
            equalTo: 'Harap masukkan nilai yang sama lagi...',
            accept: 'Harap masukkan nilai dengan ekstensi valid...',
            maxlength: $.validator.format('Harap masukkan tidak lebih dari {0} karakter...'),
            minlength: $.validator.format('Harap masukkan sedikitnya {0} karakter...'),
            rangelength: $.validator.format('Harap masukkan nilai antara {0} dan {1} karakter...'),
            range: $.validator.format('Harap masukkan nilai antara {0} dan {1}...'),
            max: $.validator.format('Harap masukkan nilai kurang dari atau sama dengan {0}...'),
            min: $.validator.format('Harap masukkan nilai lebih besar dari atau sama dengan {0}...'),
            alphanumeric: 'Harap masukkan hanya huruf dan angka',
            longlat: 'Harap masukkan hanya latitude dan longitude',
        });
        $.validator.addMethod('greaterThan', function (value, element, params) {
            if ($(params[0]).val().length && value.length) {
                return $(element).data('DateTimePicker').date().toDate() > $(params[0]).data('DateTimePicker').date().toDate();
            }
            return isNaN(value) && isNaN($(params[0]).val()) || (Number(value) > Number($(params[0]).val()));
        }, 'Nilai harus lebih besar dari {1}');
        $.validator.addMethod('lessThan', function (value, element, params) {
            if ($(params[0]).val().length && value.length) {
                return $(element).data('DateTimePicker').date().toDate() < $(params[0]).data('DateTimePicker').date().toDate();
            }
            return isNaN(value) && isNaN($(params[0]).val()) || (Number(value) > Number($(params[0]).val()));
        }, 'Nilai harus lebih kecil dari {1}');
        $('form.updateproduct').submit(function (e) {
            e.preventDefault();
        }).bind('reset', function () {

        }).validate({
            errorElement: 'span',
            errorClass: 'help-block help-block-error',
            focusInvalid: false,
            ignore: '',
            messages: {},
            rules: {
                product_name: {
                    required: true,
                },
                price: {
                    required: true,
                    digits: true,
                },
                category_id: {
                    required: true,
                },
                status_id: {
                    required: true,
                },
            },
            onfocusout: function (element) {
                $(element).valid();
            },
            invalidHandler: function (event, validator) {
            },
            errorPlacement: function (error, element) {
                if (element.hasClass('select2') && element.next('.select2-container').length) {
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.is(':checkbox')) {
                    error.insertAfter(element.closest('.md-checkbox-list, .md-checkbox-inline, .checkbox-list, .checkbox-inline'));
                } else if (element.is(':radio')) {
                    error.insertAfter(element.closest('.md-radio-list, .md-radio-inline, .radio-list,.radio-inline'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
            },
            submitHandler: function (form) {
                Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-sm btn-success ml-3',
                        cancelButton: 'btn btn-sm btn-warning ml-3',
                        denyButton: 'btn btn-sm btn-danger ml-3',
                    },
                    buttonsStyling: false,
                }).fire({
                    title: 'Konfirmasi Ubah',
                    html: 'Konfirmasi ubah data <b><?php echo $transaction->text; ?></b> ?',
                    icon: 'question',
                    showCloseButton: true,
                    confirmButtonText: 'Konfirmasi',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $('form.updateproduct').attr('action'),
                            data: $('form.updateproduct').serialize(),
                            type: 'POST',
                            success: function (data) {
								$('div#modify-data').modal('hide');
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
                                    title: 'Berhasil Diubah',
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
            },
        });
    });
</script>
