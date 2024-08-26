<div class="page-content">
    <div class="modal modal-add fade" id="loginModal" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Data</h4>
                    <button type="button" class="close btn-closed" data-bs-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <form action="#" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="last-name-column">Kode</label>
                                    <input type="text" id="txkode" class="form-control" placeholder="Kode" name="lname-column">
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="last-name-column">Nama</label>
                                    <input type="text" id="txnama" class="form-control" placeholder="Nama" name="lname-column">
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="last-name-column">Nominal</label>
                                    <input type="text" class="form-control inputnumeric angka des" name="levelgroupAmount" onkeyup="divide()" placeholder="Nominal" id="txnominal">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="last-name-column">Total Hari Dibayar</label>
                                    <input type="text" class="form-control inputnumeric angka des" name="levelgroupDivide" onkeyup="divide()" placeholder="Total Hari Dibayar" id="txtotalhari">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="last-name-column">Nominal/Hari</label>
                                    <input type="text" class="form-control inputnumeric angka des" name="levelgroupNominal" readonly="" id="txnominalperhari" readonly disabled>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="city-column">Setengah Hari</label>
                                    <fieldset class="form-group">
                                        <select class="form-select select2 select2-hidden-accessible" name="levelgroupHalfDay" onchange="cekIsHalf()" data-select2-id="1" tabindex="-1" aria-hidden="true" id="txsetengah">
                                            <option value="" disabled selected>Pilih Jenis</option>
                                            <option value="0">Digaji</option>
                                            <option value="1">Tidak Digaji</option>
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="city-column">Persen/Jumlah</label>
                                    <fieldset class="form-group">
                                        <select class="form-select" id="txpersen" name="levelgroupHalfPercent" onchange="pokok()">
                                            <option value="" disabled selected>Pilih Jenis</option>
                                            <option value="0">Persen</option>
                                            <option value="1">Jumlah</option>
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="last-name-column" id="labelPokok"> Pokok/Hari</label>
                                    <input type="text" id="txpokok" class="form-control number" name="levelgroupHalfAmount" onkeyup="validateInput(); comJumlah()">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="btn-closed d-none d-sm-block ">Close</span>
                                </button>
                                <button type="button" onclick="simpan_data()" class="btn btn-primary btn-submit ms-1">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Simpan</span>
                                </button>
                                <button type="button" onclick="update_data()" class="btn btn-primary btn-update ms-1">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Update</span>
                                </button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="card card-dta">
    <div class="card-header">
        <h5 class="card-title">
            Form Jabatan
        </h5>
        <button class="btn btn-success btn-add" onclick="showModal()" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fas fa-plus"> </i> Tambah Data</button>
        <button class="btn btn-primary" onclick="load_data()"><i class="fas fa-sync-alt"> </i> Refresh</button>
    </div>
    <div class="card-body">
        <div class="table-responsive datatable-minimal">
            <table class="table" id="table3">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Nominal</th>
                        <th>Aktif</th>
                        <th>Edit</th>
                        <th>Delete</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>
</div>
</div>
</section>
</div>
</section>
</div>
</div>


</body>

</html>