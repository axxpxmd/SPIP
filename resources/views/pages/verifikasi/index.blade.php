@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-building mr-1"></i>
                        {{ $title }}
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid my-3 relative animatedParent animateOnce">
        <div class="tab-content " id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="card no-b mb-2">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="tahun_id" class="col-form-label s-12 col-md-4 text-right font-weight-bold">Tahun : </label>
                            <div class="col-sm-4">
                                <select name="tahun_id" id="tahun_id" class="select2 form-control r-0 light s-12" onchange="selectOnChange()">
                                    <option value="0">Semua</option>
                                    @foreach ($tahuns as $i)
                                        <option {{ $i->tahun == $year ? 'selected' : '-' }} value="{{ $i->id }}">{{ $i->tahun }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card no-b">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <th width="5%"></th>
                                            <th width="50%">Nama Instansi</th>
                                            <th width="15%">Status Verifikasi</th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
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
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        order: [ 0, 'asc' ],
        ajax: {
            url: "{{ route($route.'api') }}",
            method: 'POST',
            data: function (data) {
                data.tahun_id = $('#tahun_id').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'nama', name: 'nama'},
            {data: 'status_verifikasi', name: 'status_verifikasi'}
        ],
    });

    function selectOnChange(){
        $('#dataTable').DataTable().ajax.reload();
    }
</script>
@endsection
