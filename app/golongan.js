function load_data() {
  $.post("golongan/load_data", {}, function (data) {
    console.log(data);
    $("#table3 > tbody").html('');
    $.each(data.levelgroup, function (idx, val) {
      var html = '<tr>';
      html += '<td>' + val['levelgroupId'] + '</td>';
      html += '<td>' + val['levelgroupCode'] + '</td>';
      html += '<td>' + val['levelgroupName'] + '</td>';
      html += '<td>' + val['levelgroupNominal'] + '</td>';
      html += '<td><span onclick="active_data(' + val['levelgroupId'] + ',' + val['levelgroupActive'] + ')" class="badge ' + ((val['levelgroupActive'] == '1') ? 'bg-success' : 'bg-danger') + '">' + ((val['levelgroupActive'] == '1') ? 'Aktif' : 'Non-Aktif') + '</span></td>';
      html += '<td><button class="btn btn-warning btn-sm btn-edit" onclick="edit_data(' + val['levelgroupId'] + ')"><i class="fas fa-edit"> </i></button></td>';
      html += '<td><button class="btn btn-danger btn-sm" onClick="hapus_data(' + val['levelgroupId'] + ')"><i class="fas fa-trash"></i></button></td>';
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
  $(".page-title").html("Golongan");
  $(".btn-closed").click(function () {
    reset_form();
  });

  $('#txpersen').on('change', function() {
    var selectedValue = $(this).val(); 
    
    if (selectedValue === '0') {
        $('#labelPokok').text('Persen');  
    } else if (selectedValue === '1') {
        $('#labelPokok').text('Jumlah');  
    }
});

$('#loginModal').on('hidden.bs.modal', function () {
  $('#txsetengah').val(''); 
  $('#txpersen').prop('disabled', true);
  $('#txpokok').prop('disabled', true);
});

  $("body").on('keyup', '.angka.des', function (e) {
    if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
      this.value = this.value.replace(/[^0-9\.]/g, '');
    }
    $(this).val(desimal($(this).val()));
  });

  // ketika input dengan class angka focus, maka aluenya diselect semua
  $("body").on('focus', '.angka', function (e) {
    $(this).select();
  });

  $(".angka").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and . 188 untuk koma

    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 107, 189]) !== -1 ||
      // Allow: Ctrl+A
      (e.keyCode == 65 && e.ctrlKey === true) ||
      // Allow: Ctrl+C
      (e.keyCode == 67 && e.ctrlKey === true) ||
      // Allow: Ctrl+X
      (e.keyCode == 88 && e.ctrlKey === true) ||
      // Allow: home, end, left, right
      (e.keyCode >= 35 && e.keyCode <= 39)) {
      // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
  });
  load_data();
  cekIsHalf();
});


function divide() {
  var amt = parseFloat($('[name="levelgroupAmount"]').val().replace(/,/gi, ""));
  var div = parseFloat($('[name="levelgroupDivide"]').val().replace(/,/gi, ""));
  $('[name="levelgroupNominal"]').val("");
  if (amt > 0 && div > 0) {
    nom = parseInt(amt / div);
    $('[name="levelgroupNominal"]').val(desimal(nom));
  }
}

function cekIsHalf() {
  let setengahValue = $('#txsetengah').val();

  if (!setengahValue || setengahValue === "1") {
      // Jika tidak ada nilai atau memilih "Tidak Digaji", disable input dan reset nilainya
      $('#txpersen').prop('disabled', true).val('');
      $('#txpokok').prop('disabled', true).val('');
  } else {
      // Jika ada nilai yang dipilih selain "Tidak Digaji", enable input
      $('#txpersen').prop('disabled', false);
      $('#txpokok').prop('disabled', false);
  }
}

// function toggleValidation() {
//   let selection = document.getElementById("txgaji").value;
//   let inputField = document.getElementById("txpersenpokok");

//   if (selection == "0") {
//       // Mengubah label sesuai dengan pilihan persentase
//       document.getElementById("label-persent-amount").innerText = "Persen Pokok / Perhari";
//       inputField.placeholder = "Masukkan Berapa Persen";
//   } else if (selection == "1") {
//       // Mengubah label sesuai dengan pilihan amount
//       document.getElementById("label-persent-amount").innerText = "Amount / Perhari";
//       inputField.placeholder = "Masukkan Amount";
//   }
// }

function validateInput() {
  let selection = document.getElementById("txpersen").value;
  let inputField = document.getElementById("txpokok");

  if (selection == "0") { 
      // Jika pilih "persent"
      onmaxPersent(inputField, 100);
      onminPersent(inputField, 0);
  } else if (selection == "1") {
      // Jika pilih "amount"
      onmaxAmount(inputField);
      onminAmount(inputField);
  }
}

function onmaxPersent(input, max) {
  if (input.value > max) {
      input.value = max;
  }
}

function onminPersent(input, min) {
  if (input.value < min) {
      input.value = min;
  }
}

function onmaxAmount(input) {
  let amount = parseFloat(document.getElementById("txnominalperhari").value.replace(/,/gi, "")) || 0;

  if (parseFloat(input.value) > amount) {
      input.value = amount;
  }
}

function onminAmount(input) {
  if (parseFloat(input.value) < 0) {
      input.value = 0;
  }
}

function desimal(input) {
  var output = input
  if (parseFloat(input)) {
    input = new String(input); // so you can perform string operations
    var parts = input.split("."); // remove the decimal part
    parts[0] = parts[0].split("").reverse().join("").replace(/(\d{3})(?!$)/g, "$1,").split("").reverse().join("");
    output = parts.join(".");
  }

  return output;
}

// function comJumlah (value) {
//   value = (typeof value === "number" ? value.toString() : value).replace(/,/g, "");
//   var parts = value.split(".");
//   parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
//   return parts.join(".");
// }

function comJumlah(value) {
  // Ensure value is a string
  if (typeof value !== "string") {
      value = String(value || '');
  }
  
  // Remove any existing commas
  value = value.replace(/,/g, "");

  // Split the value into integer and decimal parts
  var parts = value.split(".");

  // Format the integer part with commas
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

  // Join the integer and decimal parts, if any
  return parts.join(".");
}


function simpan_data() {
  let kode = $("#txkode").val();
  let nama = $("#txnama").val();
  let nominal = $("#txnominal").val();
  let total_hari = $("#txtotalhari").val();
  let nominal_perhari = $("#txnominalperhari").val();
  let setengah = $("#txsetengah").val();
  let persen = $("#txpersen").val();
  let pokok = $("#txpokok").val();

  console.log({
    txkode: kode,
    txnama: nama,
    txnominal: nominal,
    txtotalhari: total_hari,
    txnominalperhari: nominal_perhari,
    txsetengah: setengah,
    txpersen: persen,
    txpokok: pokok,
  });

  if (kode === "" || nama === "" || nominal === "" || total_hari === "" || nominal_perhari === "" || setengah === "") {
    Swal.fire({
      title: 'Data Tidak Lengkap',
      text: 'Data yang dimasukkan masih belum lengkap',
      icon: 'warning',
      confirmButtonText: 'OK'
    });
  } else {
    $.post("golongan/create", {
      txkode: kode,
      txnama: nama,
      txnominal: nominal,
      txtotalhari: total_hari,
      txnominalperhari: nominal_perhari,
      txsetengah: setengah,
      txpersen: persen,
      txpokok: pokok,
    }, function (data) {
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


    $.post('employment/load_department', function (res) {
      $("#txdepartment").empty();
      $("#txdepartment").append('<option value="">Pilih Department</option>');
      $.each(res.data_department, function (i, v) {
        let selected = (v.departmentId == data.data.employmentDepartmentId) ? 'selected' : '';
        $("#txdepartment").append('<option value="' + v.departmentId + '" ' + selected + '>' + v.departmentName + '</option>');
      });
    }, 'json');


    $.post('employment/load_department', { id: data.data.employmentDepartmentId }, function (res) {
      $("#txatasan").empty();
      $("#txatasan").append('<option value="">Pilih Atasan</option>');
      $.each(res.data_employment, function (i, v) {
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


function update_data() {
  var id = $("#loginModal").data('id');
  let employmentDepartmentId = $("#txdepartment").val();
  let employmentParentEmploymentId = $("#txatasan").val();
  let employmentCode = $("#txkode").val();
  let employmentName = $("#txnama").val();

  if (employmentDepartmentId === "" || employmentParentEmploymentId === "" || employmentCode === "" || employmentName === "") {
    Swal.fire({
      title: 'Error!',
      text: data.msg,
      icon: 'error',
      confirmButtonText: 'OK'
    })
  } else {
    $.post('employment/update_table', { id: id, employmentDepartmentId: employmentDepartmentId, employmentParentEmploymentId: employmentParentEmploymentId, employmentCode: employmentCode, employmentName: employmentName }, function (data) {
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
  }
}

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