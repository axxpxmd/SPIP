@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-user-o mr-2"></i>
                        {{ $role->name }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li class="nav-item">
                        <a class="nav-link active show" href="{{ route('pengguna.index') }}"><i class="icon icon-home2"></i>Semua Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="row">
                <div class="col-md-12">
                    <div id="alert"></div>
                    <div class="card">
                        <h6 class="card-header"><strong>Tambah Data</strong></h6>
                        <div class="card-body">
                            <form class="needs-validation" id="form" method="POST"  enctype="multipart/form-data" novalidate>
                                {{ method_field('POST') }}
                                <input type="hidden" name="roleId" value="{{ $roleId }}">
                                <input type="hidden" name="tempatId" value="{{ $tempatId }}">
                                <div class="form-row form-inline">
                                    @if ($roleId == 5)
                                    <div class="col-md-8">
                                        <!-- user -->
                                        <div class="form-group m-0">
                                            <label for="username" class="text-right s-12 col-md-2">Username<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="username" id="username" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <!-- pegawai -->
                                        <div class="form-group m-0">
                                            <label for="nama_instansi" class="col-form-label s-12 col-md-2">Nama Instansi<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="nama_instansi" id="nama_instansi" class="form-control r-0 light s-12 col-md-6" autocomplete="off" value="{{ $tempat->n_tempat }}" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="nama_kepala" class="col-form-label s-12 col-md-2">Nama Kepala<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="nama_kepala" id="nama_kepala" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="jabatan_kepala" class="col-form-label s-12 col-md-2">Jabatan Kepala<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="jabatan_kepala" id="jabatan_kepala" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="nama_operator" class="col-form-label s-12 col-md-2">Nama Asesor<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="nama_operator" id="nama_operator" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="jabatan_operator" class="col-form-label s-12 col-md-2">Jabatan Asesor<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="jabatan_operator" id="jabatan_operator" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="email" class="col-form-label s-12 col-md-2">Email<span class="text-danger ml-1">*</span></label>
                                            <input type="email" name="email" id="email" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="telp" class="col-form-label s-12 col-md-2">No Telp<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="telp" id="telp" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="alamat" class="col-form-label s-12 col-md-2">Alamat<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="alamat" id="alamat" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group mt-2">
                                            <div class="col-md-2"></div>
                                            <button type="submit" class="btn btn-primary btn-sm" id="action"><i class="icon-save mr-2"></i>Simpan<span id="txtAction"></span></button>
                                            <a class="btn btn-sm" onclick="add()" id="reset">Reset</a>
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-8">
                                        <!-- user -->
                                        <div class="form-group m-0">
                                            <label for="username" class="text-right s-12 col-md-2">Username<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="username" id="username" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="nama_instansi" class="col-form-label s-12 col-md-2">Nama<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="nama_instansi" id="nama_instansi" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        <div class="form-group m-0">
                                            <label for="telp" class="col-form-label s-12 col-md-2">No Telp<span class="text-danger ml-1">*</span></label>
                                            <input type="text" name="telp" id="telp" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required/>
                                        </div>
                                        @if ($roleId == 6)
                                        <div class="form-group mb-1" id="display_perangkat_daerah">
                                            <label class="col-form-label s-12 col-md-2">Perangkat Daerah <span class="text-danger ml-1">*</span></label>
                                            <div class="col-md-6 p-0 bg-light">
                                                <select name="opds[]" id="opds" placeholder="" class="select2 form-control r-0 light s-12" multiple="multiple" required>
                                                    @foreach($opds as $key=>$opds)
                                                    <option value="{{ $opds->id }}">{{ $opds->n_tempat }}</option>
                                                    @endforeach
                                                <select>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="form-group mt-2">
                                            <div class="col-md-2"></div>
                                            <button type="submit" class="btn btn-primary btn-sm" id="action"><i class="icon-save mr-2"></i>Simpan<span id="txtAction"></span></button>
                                            <a class="btn btn-sm" onclick="add()" id="reset">Reset</a>
                                        </div>
                                    </div>
                                    @endif
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
    function add(){
        save_method = "add";
        $('#form').trigger('reset');
        $('input[name=_method]').val('POST');
        $('#txtAction').html('');
        $('#reset').show();
        $('#n_pegawai').focus();
    }

    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#alert').html('');
            url = "{{ route($route.'store') }}",
            $.ajax({
                url : url,
                type : 'POST',
                data: new FormData(($(this)[0])),
                contentType: false,
                processData: false,
                success : function(data) {
                    console.log(data);
                    $('#alert').html("<div role='alert' class='alert alert-success alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Success!</strong> " + data.message + "</div>");
                    window.location.href = "{{ route($route.'index') }}";
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
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
    });
</script>
@endsection
