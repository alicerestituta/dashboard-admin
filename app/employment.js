function load_data() {
    $.post("employment/load_data", {}, function (data) {
        console.log(data);
        $("#table3 > tbody").html('');
        $.each(data.employment, function (idx, val) {
            var html = '<tr>';
            html += '<td>' + val['employmentId'] + '</td>';
            html += '<td>' + val['employmentCode'] + '</td>';
            html += '<td>' + val['departmentName'] + '</td>';
            html += '<td>' + val['nama'] + '</td>';
            html += '<td>' + val['atasan'] + '</td>';
            html += '<td><span onclick="active_data(' + val['employmentId'] + ',' + val['employmentActive'] + ')" class="badge ' + ((val['employmentActive'] == '1') ? 'bg-success' : 'bg-danger') + '">' + ((val['departmentActive'] == '1') ? 'Aktif' : 'Non-Aktif') + '</span></td>';
            html += '<td><button class="btn btn-warning btn-sm btn-edit" onclick="edit_data(' + val['employmentId'] + ')"><i class="fas fa-edit"> </i></button></td>';
            html += '<td><button class="btn btn-danger btn-sm" onClick="hapus_data(' + val['employmentId'] + ')"><i class="fas fa-trash"></i></button></td>';
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

    $(document).ready(function () {
        $(".page-title").html("Jabatan");
        $(".btn-closed").click(function () {
            reset_form();
        });
        load_data();
        load_department();
    });

    function load_department() {
      $.post("employment/load_department", function (res) {
          $('#txdepartment').empty(); 
          $('#txdepartment').append('<option value="" disabled selected>Pilih Department</option>'); 
          $('#txatasan').append('<option value="" disabled selected>Pilih Atasan</option>'); 
  
          $.each(res.data_department, function (i, v) {
              $('#txdepartment').append('<option value="' + v.departmentId + '">' + v.departmentName + '</option>');
          });
          $.each(res.data_employment, function (i, v) {
              $('#txatasan').append('<option value="' + v.employmentId + '">' + v.nama + '</option>');
          });
      }, 'json').fail(function () {
          console.error('Error loading division data');
      });
  }

  function load_employment(id) {
    $.post("employment/load_department",{id:id}, function (res) {
        $('#txatasan').empty();  
        $('#txatasan').append('<option value="" disabled selected>Pilih Atasan</option>'); 
        $.each(res.data_employment, function (i, v) {
            $('#txatasan').append('<option value="' + v.employmentId + '">' + v.atasan + '</option>');
        });
    }, 'json').fail(function () {
        console.error('Error loading division data');
    });
}

    function simpan_data() {
      let department = $("#txdepartment").val();
      let atasan = $("#txatasan").val();
      let kode = $("#txkode").val();
      let nama = $("#txnama").val();
  
      console.log({
        txdepartment: department,
        txatasan: atasan,
        txkode: kode,
        txnama: nama,
      });
  
      if (department === "" || atasan === "" || kode === "" || nama === "") {
          Swal.fire({
              title: 'Data Tidak Lengkap',
              text: 'Data yang dimasukkan masih belum lengkap',
              icon: 'warning',
              confirmButtonText: 'OK'
          });
      } else {
          $.post("employment/create", {
            txdepartment: department,
            txatasan: atasan,
            txkode: kode,
            txnama: nama,
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

  function edit_data(id) {
    $.post('employment/edit_table', { id: id }, function (data) {
        $("#txkode").val(data.data.employmentCode);
        $("#txnama").val(data.data.employmentName);
  
        
        $.post('employment/load_department', function(res) {
            $("#txdepartment").empty();
            $("#txdepartment").append('<option value="">Pilih Department</option>');
            $.each(res.data_department, function(i, v) {
                let selected = (v.departmentId == data.data.employmentDepartmentId) ? 'selected' : '';
                $("#txdepartment").append('<option value="'+v.departmentId+'" '+selected+'>'+v.departmentName+'</option>');
            });
        }, 'json');
  
      
        $.post('employment/load_department', { id: data.data.employmentDepartmentId }, function(res) {
            $("#txatasan").empty();
            $("#txatasan").append('<option value="">Pilih Atasan</option>');
            $.each(res.data_employment, function(i, v) {
                let selected = (v.employmentId == data.data.employmentParentEmploymentId) ? 'selected' : '';
                $("#txatasan").append('<option value="' + v.employmentId + '" ' + selected + '>' + v.atasan + '</option>');
            });
        }, 'json');
  
        $("#loginModal").data('id', id); 
        $("#loginModal").modal('show');
        $(".btn-submit").hide();
        $(".btn-update").show();
    }, 'json');
  }

  
function update_data(){
    var id = $("#loginModal").data('id');
    let employmentDepartmentId = $("#txdepartment").val();
    let employmentParentEmploymentId = $("#txatasan").val();
    let employmentCode = $("#txkode").val();
    let employmentName = $("#txnama").val();
    
    if (employmentDepartmentId === "" || employmentParentEmploymentId === ""|| employmentCode === "" || employmentName === ""){
      Swal.fire({
        title: 'Error!',
        text: data.msg,
        icon: 'error',
        confirmButtonText: 'OK'
      })
    }else{
      $.post('employment/update_table', { id: id, employmentDepartmentId: employmentDepartmentId, employmentParentEmploymentId: employmentParentEmploymentId, employmentCode: employmentCode, employmentName: employmentName}, function(data) {
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
        $.post('employment/delete_table', { id: id }, function (data) {
          if (data.status === 'success') {
            Swal.fire({
              title: 'Berhasil!',
              text: 'Data berhasil dihapus',
              icon: 'success',
              confirmButtonText: 'OK'
            }).then(() => {
              location.reload(); 
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
        Swal.fire({
          title: 'Dibatalkan',
          text: 'Penghapusan data dibatalkan!',
          icon: 'info',
          confirmButtonText: 'OK'
        });
      }
    });
  }

  function showModal() {
    $(".btn-submit").show();
    $(".btn-update").hide();
  }