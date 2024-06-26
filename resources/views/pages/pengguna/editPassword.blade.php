@extends('layouts.app')
@section('title', '| Data Pegawai')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-user-o"> </i>
                        Edit Password {{ $title }} | {{ $user->pegawai->nama_instansi }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route($route.'editPassword', $user->id) }}" class="nav-link active show"><i class="icon icon-key4"></i>Ganti Password</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid">
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="row">
                <div class="col-md-12">
                    <div id="alert"></div>
                    <div class="card">
                        <h6 class="card-header"><strong>Ganti Password</strong></h6>
                        <div class="card-body">
                            <form class="needs-validation" id="form" method="PATCH"  enctype="multipart/form-data" novalidate>
                                {{ method_field('POST') }}
                                <input type="hidden" id="user_id" name="id" value="{{ $user->id }}"/>
                                <div class="form-row form-inline">
                                    <div class="col-md-8">
                                        <div class="form-group m-0">
                                            <label for="username" class="col-form-label s-12 col-md-2">Password</label>
                                            <input type="password" name="password" id="password" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="username" class="col-form-label s-12 col-md-2">Konfirmasi Password</label>
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group mt-2">
                                            <div class="col-md-2"></div>
                                            <button type="submit" class="btn btn-primary btn-sm"><i class="icon-save mr-2"></i>Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#alert').html('');
            $('#action').attr('disabled', true);
            url = "{{ route($route.'updatePassword', ':id') }}".replace(':id', $('#user_id').val());
            $.ajax({
                url : url,
                type : 'POST',
                data: new FormData(($(this)[0])),
                contentType: false,
                processData: false,
                success : function(data) {
                    console.log(data);
                    $.confirm({
                        title: 'Success',
                        content: data.message,
                        icon: 'icon icon-check',
                        theme: 'modern',
                        closeIcon: true,
                        animation: 'scale',
                        autoClose: 'ok|3000',
                        type: 'green',
                        buttons: {
                            ok: {
                                text: "ok!",
                                btnClass: 'btn-primary',
                                keys: ['enter'],
                                action: function () {
                                    location.reload();
                                }
                            }
                        }
                    });
                },
                error : function(data){
                    err = '';
                    respon = data.responseJSON;
                    if(respon.errors){
                        $.each(respon.errors, function( index, value ) {
                            err = err + "<li>" + value +"</li>";
                        });
                    }
                    $('#alert').html("<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Error!</strong> " + respon.message + "<ol class='pl-3 m-0'>" + err + "</ol></div>");
                    $('#action').removeAttr('disabled');
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
    });

</script>
@endsection