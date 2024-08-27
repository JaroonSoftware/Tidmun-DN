

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

		$.post("https://tidmunzbuffet.com/api_app/dn/add_dnmaster.php", { socode: $('#socode').val(), cuscode: $('#cuscode').val() }, function (response2) {

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

	const ipc = require("electron").ipcRenderer;
	ipc.send('createDN', $('#dncode').val());
}

var count = 0;

const { SerialPort } = require('serialport')
const { ReadlineParser } = require('@serialport/parser-readline')
const port = new SerialPort({ path: 'COM1', baudRate: 9600 }, function (err) {
	if (err) {
		document.getElementById('txtresult').innerHTML = 'เชื่อมต่อเครื่องชั่งน้ำหนักไม่สำเร็จ'
		document.getElementById('txtresult').style.color = "red";
		return console.log('Error: ', err.message)
	}
})

const parser = port.pipe(new ReadlineParser({ delimiter: '\r\n' }))
parser.on('data', addText)

function addText(event) {
	// console.log(event)
	document.getElementById("tx_unitweigt").value = parseFloat(event.substring(1, 8)).toFixed(2);
}

var inputbarcode = document.getElementById("tx_barcode");
var inputweight = document.getElementById("tx_unitweigt");
var str;

inputbarcode.addEventListener("keypress", function (event) {
	if (event.key === "Enter") {

		if (inputweight.value > 0) {

			$.post("https://tidmunzbuffet.com/api_app/dn/add_dndetail.php", { socode: $('#socode').val(), dncode: $('#dncode').val(), barcode_id: inputbarcode.value }, function (response2) {

				let r2 = JSON.parse(response2)

				
				// $('#tableDN tbody').empty();
				if(r2.status)
				{
					let count = $('#tableDN').find('tr').length;
					tb = '';
					tb += '<tr id="tr' + count + '"><td>' + count + '</td><td>' + r2.data.stcode + '</td><td>' + r2.data.stname + '</td><td>' + r2.data.unit_weight + '</td><td>' + r2.data.datetimelog + '</td>';
					tb += '</tr>';
					$(tb).appendTo("#tableDN");

					document.getElementById('txtresult').style.color = "#4DBE05";
					$('#txtresult').text('เพิ่มสินค้า ' + r2.data.stname + ' สำเร็จ')
				}
				else
				{
				$('#txtresult').text(r2.message)
				document.getElementById('txtresult').style.color = "red";
				}

			}).fail(function (error) {

				$('#txtresult').text('อินเตอร์เน็ตมีปัญหา เชื่อมต่อไม่ได้')
			});




		} else {

			document.getElementById('txtresult').innerHTML = 'กรุณาชั่งสินค้าก่อน ยิง Barcode'
			document.getElementById('txtresult').style.color = "red";

		}
		inputbarcode.value = null;
		inputbarcode.click();
		event.preventDefault();
	}
});