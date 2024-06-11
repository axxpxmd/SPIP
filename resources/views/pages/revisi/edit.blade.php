@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<style>
    .custom-file-input ~ .custom-file-label::after {
        content: "Pilih";
    }
</style>
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-document-text6"></i>
                        Edit Data Quesioner Tahun {{ $tahun }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'show',$tahunId) }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        @include('layouts.alert')
                        <form class="needs-validation" action="{{ route('revisi.update', $data->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="total_kuesioner" value="{{ $data->quesioner->count() }}">
                            <div class="card">
                                <h6 class="card-header font-weight-bold text-black">Edit Kuesioner</h6>
                                <div class="card-body">
                                    <p class="font-weight-bold text-black ml-3">{{ $data->quesioner->indikator->n_indikator }}</p>
                                    <div class="ml-2 mb-2" style="margin-top: -15px !important">
                                        <span>{{ $data->quesioner->indikator->deskripsi }}</span>
                                    </div>
                                    <div class="ml-5">
                                        <span class="text-black font-weight-normal mt-2">{{ $data->quesioner->question->n_question }}</span>
                                        @foreach ($answers as $index2 => $a)
                                        <div class="form-check mt-1">
                                            <input type="radio" class="form-check-input" id="answer_id{{ $index2 }}" name="answer_id" value="{{ $a->answer->id }}" {{ $a->answer->id == $data->answer->id ? "checked" : "-" }} >
                                            <label for="answer_id{{ $index2 }}" class="form-check-label text-black font-weight-normal fs-14">{{ $a->answer->jawaban }} </label>
                                        </div>
                                        @endforeach
                                        @if ($data->message)
                                        <div class="mt-2">
                                            <span class="font-weight-normal text-black"><strong class="text-black">Pesan Revisi :</strong> {{ $data->message }} </span>
                                        </div>
                                        @endif
                                        <div class="mt-2">
                                            <span class="font-weight-normal text-black"><strong class="text-black">Keterangan :</strong> {{ $data->keterangan }} </span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="font-weight-bold"><strong class="text-black">File :</strong></span>
                                            <ol class="ml-3 text-black" style="margin-top: -20px !important">
                                            @forelse ($files as $f)
                                                <li class="mt-0">
                                                    <a target="blank" href="{{ config('app.sftp_src').$path.$f->file }}"> {{ $f->file }} </a>
                                                    <a class="text-danger" href="" data-toggle="modal" data-target="#deleteFile{{ $f->id }}"><i class="icon icon-trash ml-1"></i></a>
                                                </li>
                                            @empty
                                                <span>-</span>
                                            @endforelse
                                            </ol>
                                        </div>
                                        <hr>
                                        <div class="mt-2">
                                            <div class="row">
                                                <div class="col-md-12 text-black ml-4">
                                                    <div class="row m-0">
                                                        <label class="col-sm-1 font-weight-normal text-black">File</label>
                                                        <div class="custom-file col-md-6">
                                                            <input type="file" class="custom-file-input" id="file" name="file[]" onchange="javascript:updateList()" multiple {{ count($files) == 0 ? 'required' : '-' }} onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode==32)">
                                                            <label class="custom-file-label" for="customFile" id="fileTitle">Pilih File</label>
                                                        </div>
                                                    </div>
                                                    <div class="row m-0 mt-2" style="margin-left: 112px !important">
                                                        <div class="col-md-6 " id="fileList"></div>
                                                    </div>
                                                    <div class="row m-0 mt-2">
                                                        <label class="col-sm-1 font-weight-normal text-black">Keterangan</label>
                                                        <textarea name="keterangan" rows="5" class="form-control col-md-6" style="font-size: 14px !important" autocomplete="off">{{ $data->keterangan }}</textarea>
                                                    </div>
                                                    <div class="row m-0 mt-2" style="margin-left: 112px !important">
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary btn-sm"><i class="icon-save mr-2"></i>Simpan Perubahan</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach ($files as $f)
    <div class="modal fade" id="deleteFile{{ $f->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title font-weight-normal text-black fs-14" id="exampleModalLabel">
                        Apakah anda yakin ingin menghapus file ini ?
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer border-0 p-2">
                    <form action="{{ route('revisi.deleteFile', $f->id) }}" method="GET">
                        {{ csrf_field() }}
                        {{ method_field('get') }}
                        <input type="hidden" value="{{ $userId }}">
                        <input type="hidden" value="{{ $tahunId }}">
                        <button class="btn btn-sm btn-danger"><i class="icon-delete mr-2"></i>Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $("#successAlert").fadeTo(5000, 1000).slideUp(1000, function() {
            $("#successAlert").slideUp(1000);
        });
    });

    $(document).ready(function() {
        $("#errorAlert").fadeTo(5000, 1000).slideUp(1000, function() {
            $("#errorAlert").slideUp(1000);
        });
    });

    updateList = function() {
        var input = document.getElementById('file');
        var output = document.getElementById('fileList');
        var children = "";
        for (var i = 0; i < input.files.length; ++i) {
            children += '<li type="1">'+ input.files.item(i).name + '</li>';
        }
        output.innerHTML = children;
        $('#fileTitle').html(input.files.length + ' File dipilih');
    }
</script>
@endsection
