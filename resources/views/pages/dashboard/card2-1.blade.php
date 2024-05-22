<div class="card">
    <div class="card-header white">
        <div class="row justify-content-end">
            <div class="col">
                <ul class="nav nav-tabs card-header-tabs nav-material">
                    <li class="nav-item">
                        <a class="nav-link active show" id="w1-tab1" data-toggle="tab" href="#v-pills-w1-tab1">Keseluruhan Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body no-p">
        <div class="tab-content">
            <div class="tab-pane animated fadeInRightShort show active" id="v-pills-w1-tab1" role="tabpanel" aria-labelledby="v-pills-w1-tab1">
                <div class="row p-3">
                    <div class="col-md-6">
                        {{-- @include('pages.dashboard.charts') --}}
                    </div>
                    <div class="col-md-6">
                        <div class="card-body pt-0">
                            <h6></h6>
                            <div class="my-3">
                                <div class="float-right">
                                    <a href="" class="btn-fab btn-fab-sm btn-primary r-5">
                                        <i class="icon-mail-envelope-closed2 p-0"></i>
                                    </a>
                                    <a href="" class="btn-fab btn-fab-sm btn-success r-5">
                                        <i class="icon-star p-0"></i>
                                    </a>
                                </div>
                                <div class="mr-3 float-left">
                                    <img src="{{ asset('images/logo/tangsel.png') }}" width="50" alt="">
                                </div>
                                <div>
                                    <div>
                                        <strong>DISDUKCAPIL KOTA TANGERANG SELATAN</strong>
                                    </div>
                                    <small>Dinas Kependudukan dan Catatan Sipil</small>
                                </div>
                                <div class="mt-5">
                                    <table class="table table-striped table-hover table-bordered ">
                                        <tbody>
                                        <tr>
                                            <th>Grab</th>
                                            <th>Gojek</th>
                                            <th>Total</th>
                                        </tr>
                                        <tr>
                                            <td class="sc-counter">{{ $grab }}</td>
                                            <td class="sc-counter">{{ $gojek }}</td>
                                            <td class="sc-counter">{{ $total }}</td>
                                        </tr>
                                        </tbody>
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