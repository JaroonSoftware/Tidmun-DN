const { ipcRenderer } = require("electron");

var sodata;

ipcRenderer.on("got-access-token", (event, accessToken) => {
//   data = accessToken.split(",");
  let socode = accessToken;

  $.post(REACT_APP_BACKEND_URL+"/api_app/so/getsup_so.php", { socode : socode }, function (sohead) {
	// console.log(grhead);
    let result = JSON.parse(sohead)
	$('#socode').val(socode)
	$('#cuscode').val(result[0].cuscode)
	$('#sodate').val(result[0].sodate)
	$('#cusname').val(result[0].cusname	)
  
  });

  $.post(REACT_APP_BACKEND_URL+"/api_app/so/getsup_sodetail.php", { socode : socode }, function (sodetail) {
	// console.log(grdetail);
    let result = JSON.parse(sodetail)
    $('#tbmain tbody').empty();

    sodata=result

    for (let i in result) {
		let count = (parseInt(i)+1)
      tb = '';
      tb += '<tr id="' + (i + 1) + '"><td>' + count + '</td><td class="nr">' + result[i].stcode + '</td><td>' + result[i].stname + '</td><td>' + result[i].qty + '</td><td>' + result[i].delamount + '</td>';
      tb += '</tr>';
      $(tb).appendTo("#tbmain");
    }

    $('#tx_barcode').attr("placeholder", "");
    $('#tx_barcode').prop("disabled", true);
    $("#btnStartScan").show();
    $("#btnCreate").hide();
    $('#dncode').val('')

  }).fail(function (error) {

    $('#txtresult').text('อินเตอร์เน็ตมีปัญหา เชื่อมต่อไม่ได้') 
  });

});

ipcRenderer.on("submit-dn", (event, accessToken) => {
	$('#btnStartScan').show()
	$('#btnCreate').hide()
	$('#dncode').val('')
	$('#tx_barcode').attr("placeholder", "");
	$('#tx_barcode').prop("disabled", true);
	document.getElementById('txtresult').style.color = "#4DBE05";
	$('#txtresult').text('สร้างใบส่งของสำเร็จ ' + accessToken + ' สำเร็จ')
});