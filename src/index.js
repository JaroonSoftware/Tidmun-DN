

// $(document).mousemove(function (event) {
// 	$("#tx_barcode").focus();
// });

function ChangeSO() {

	const ipc = require("electron").ipcRenderer;
	ipc.send('message:loginShow');

}
function checkBarcode() {

	const ipc = require("electron").ipcRenderer;
	ipc.send('message:loginShow2');

}

function startScan() {

	if ($('#socode').val() !== '') {
		$('#tx_barcode').attr("placeholder", "สแกนบาร์โค้ดที่นี่");
		$('#tx_barcode').prop("disabled", false);
		$("#tx_barcode").focus();
		$("#btnStartScan").hide();
		$("#btnCreate").show();
		$('#tableDN tbody').empty();

		$.post("https://tidmunzbuffet.com/api_app/dn/get_dncode.php", {}, function (response2) {

			let r2 = JSON.parse(response2)
			$('#dncode').val(r2.dncode)
		}).fail(function (error) {

			$('#txtresult').text('อินเตอร์เน็ตมีปัญหา เชื่อมต่อไม่ได้')
		});

	}
	else {
		Swal.fire({
			title: "<strong>กรุณาเลือกใบขายสินค้าก่อน</strong>",
			icon: "error",
		});
	}
}

function createDN() {

	if ($('#tableDN tbody tr').length != 0) {
		const ipc = require("electron").ipcRenderer;
		ipc.send('createDN', $('#dncode').val());
	}
	else {
		Swal.fire({
			title: "<strong>กรุณายิงBarcodeก่อน</strong>",
			icon: "error",
		});
	}
}

var inputbarcode = document.getElementById("tx_barcode");

inputbarcode.addEventListener("keypress", function (event) {
	if (event.key === "Enter") {

		let inputvalue = inputbarcode.value
		let result
		$.post("https://tidmunzbuffet.com/api_app/barcode/check_barcode.php", { barcode_id: inputvalue }, function (grdetail) {
			result = JSON.parse(grdetail)
			
			let tmp = sodata.find(x => x.stcode === result.stcode)

			if (tmp != null) {
				$.post("https://tidmunzbuffet.com/api_app/dn/add_dndetail.php", { socode: $('#socode').val(), dncode: $('#dncode').val(), cuscode: $('#cuscode').val(), barcode_id: inputvalue }, function (response2) {

					let r2 = JSON.parse(response2)
					// console.log(r2)

					if(r2.status)
					{
						let count = $('#tableDN').find('tr').length;
						tb = '';
						tb += '<tr id="tr' + count + '"><td>' + count + '</td><td>' + r2.data.stcode + '</td><td>' + r2.data.stname + '</td><td>' + r2.data.unit_weight + '</td><td>' + r2.data.datetimelog + '</td>';
						tb += '</tr>';
						$(tb).appendTo("#tableDN");

						document.getElementById('txtresult').style.color = "#4DBE05";
						$('#txtresult').text('เพิ่มสินค้า ' + r2.data.stname + ' สำเร็จ')


						$.post("https://tidmunzbuffet.com/api_app/so/getsup_sodetail.php", { socode : r2.data.stcode }, function (sodetail) {
							// console.log(grdetail);
							let result = JSON.parse(sodetail)
							$('#tbmain tbody').empty();

							for (let i in result) {
								let count = (parseInt(i)+1)
							  tb = '';
							  tb += '<tr id="' + (i + 1) + '"><td>' + count + '</td><td class="nr">' + result[i].stcode + '</td><td>' + result[i].stname + '</td><td>' + result[i].qty + '</td><td>' + result[i].delamount + '</td>';
							  tb += '</tr>';
							  $(tb).appendTo("#tbmain");
							}

							console.log(solist)

						  }).fail(function (error) {

							$('#txtresult').text('อินเตอร์เน็ตมีปัญหา เชื่อมต่อไม่ได้') 
						  });
					}
					else
					{
					$('#txtresult').text(r2.message)
					document.getElementById('txtresult').style.color = "red";
					}

				}).fail(function (error) {

					$('#txtresult').text('อินเตอร์เน็ตมีปัญหา เชื่อมต่อไม่ได้')
				});
			}
			else
				$('#txtresult').text('สินค้าไม่มีรายการในใบขายสินค้านี้')
				document.getElementById('txtresult').style.color = "red";
		})




		inputbarcode.value = null;
		inputbarcode.click();
		event.preventDefault();
	}
});