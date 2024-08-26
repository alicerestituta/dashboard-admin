function hapus_data(id) {
    Swal.fire({
      title: 'Konfirmasi!',
      text: 'Yakin ingin menghapus data?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya',
      cancelButtonText: 'Batal',
      reverseButtons: true, 
      customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('department/delete_table', { id: id }, function (data) {
          if (data.status === 'success') {
            Swal.fire({
              title: 'Berhasil!',
              text: 'Data berhasil dihapus',
              icon: 'success',
              confirmButtonText: 'OK'
            }).then(() => {
              location.reload(); // Reload the page after deletion
            });
          } else {
            Swal.fire({
              title: 'Error!',
              text: data.msg || 'Gagal menghapus data',
              icon: 'error',
              confirmButtonText: 'OK'
            });
          }
        }, 'json');
      } else {
        // If the user canceled the action
        Swal.fire({
          title: 'Dibatalkan',
          text: 'Penghapusan data dibatalkan!',
          icon: 'info',
          confirmButtonText: 'OK'
        });
      }
    });
  }

function update_data(){
    var id = $("#loginModal").data('id');
    let departmentDivisionId = $("#txdivision").val();
    let departmentCode = $("#txcode").val();
    let departmentName = $("#txname").val();
    
    if (departmentDivisionId === "" || departmentCode === ""|| departmentName === ""){
      Swal.fire({
        title: 'Error!',
        text: data.msg,
        icon: 'error',
        confirmButtonText: 'OK'
      })
    }else{
      $.post('department/update_table', { id: id, departmentDivisionId: departmentDivisionId, departmentCode: departmentCode, departmentName: departmentName}, function(data) {
        if (data.status === 'success') {
          Swal.fire({
            title: 'Success!',
            text: data.msg,
            icon: 'success',
            confirmButtonText: 'OK'
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            title: 'Error!',
            text: data.msg,
            icon: 'error',
            confirmButtonText: 'OK'
          })
        }
    }, 'json');
  }}
  
  function edit_data(id) {
    $.post('department/edit_table', { id: id }, function (data) {
      $("#txdivision").val(data.data.departmentDivisionId);
      $("#txcode").val(data.data.departmentCode);
      $("#txname").val(data.data.departmentName);
      $("#loginModal").data('id', id); 
      $("#loginModal").modal('show');
      $(".btn-submit").hide();
      $(".btn-update").show();
    }, 'json')
  }

function simpan_data() {
    let division = $("#txdivision").val();
    let kode = $("#txcode").val();
    let name = $("#txname").val();

    console.log({
        txdivision: division,
        txcode: kode,
        txname: name,
    });

    if (division === "" || kode === "" || name === "") {
        Swal.fire({
            title: 'Data Tidak Lengkap',
            text: 'Data yang dimasukkan masih belum lengkap',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    } else {
        $.post("department/create", {
            txdivision: division,
            txcode: kode,
            txname: name,
        }, function(data) {
            console.log(data.status);
            if (data.status === "error") {
                Swal.fire({
                    title: 'Error',
                    text: data.msg,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    title: 'Berhasil',
                    text: data.msg,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            }
            $(".btn-submit").show();
            $(".btn-update").hide();
        }, 'json');
    }
}

function load_data() {
    $.post("department/load_data", {}, function (data) {
        console.log(data);
        $("#table3 > tbody").html('');
        $.each(data.department, function (idx, val) {
            var html = '<tr>';
            html += '<td>' + val['departmentId'] + '</td>';
            html += '<td>' + val['departmentCode'] + '</td>';
            html += '<td>' + val['departmentName'] + '</td>';
            html += '<td>' + val['divisionName'] + '</td>';
            html += '<td><span onclick="active_data(' + val['departmentId'] + ',' + val['departmentActive'] + ')" class="badge ' + ((val['departmentActive'] == '1') ? 'bg-success' : 'bg-danger') + '">' + ((val['departmentActive'] == '1') ? 'Aktif' : 'Non-Aktif') + '</span></td>';
            html += '<td><button class="btn btn-warning btn-sm btn-edit" onclick="edit_data(' + val['departmentId'] + ')"><i class="fas fa-edit"> </i></button></td>';
            html += '<td><button class="btn btn-danger btn-sm" onClick="hapus_data(' + val['departmentId'] + ')"><i class="fas fa-trash"></i></button></td>';
            html += '</tr>';
            $("#table3 > tbody").append(html);
        });
        $("#table3").DataTable({
            responsive: true,
            processing: true,
            pagingType: "first_last_numbers",
            // order: [[0, 'asc']],
            dom:
              "<'row'<'col-3'l><'col-9'f>>" +
              "<'row dt-row'<'col-sm-12'tr>>" +
              "<'row'<'col-4'i><'col-8'p>>",
            language: {
              info: "Page _PAGE_ of _PAGES_",
              lengthMenu: "_MENU_",
              search: "",
              searchPlaceholder: "Search..",
            },
          });
        },
        "json"
      );
    }

    function load_division() {
        $.post("department/load_division", function (res) {
            $('#txdivision').empty(); 
            $('#txdivision').append('<option value="" disabled selected>Pilih Divisi</option>'); 
    
            $.each(res.data_division, function (i, v) {
                $('#txdivision').append('<option value="' + v.divisionId + '">' + v.divisionName + '</option>');
            });
        }, 'json').fail(function () {
            console.error('Error loading division data');
        });
    }
    
    $(document).ready(function () {
        $(".page-title").html("Department");
        $(".btn-closed").click(function () {
            reset_form();
        });
        // $(".btn-submit").click(function () {
        //   $('.btn-submit').show();
        //   $('.btn-update').hide();
        // });
        load_data();
        load_division();
    });

    function showModal() {
      $(".btn-submit").show();
      $(".btn-update").hide();
    }
    