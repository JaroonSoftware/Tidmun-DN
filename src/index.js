

$(document).mousemove(function (event) {
	$("#tx_empcode").focus();
});

function createBrowserWindow() {

	const ipc = require("electron").ipcRenderer;
	ipc.send('message:loginShow');

}
function createBrowserWindow2() {

	const ipc = require("electron").ipcRenderer;
	ipc.send('message:loginShow2');

}

var count = 0;
// $(document).ready(function () {
	// $.post("https://tidmunzbuffet.com/api_app/gr/getsup_grdetail.php", function (r) {
    // console.log(r)
    // console.log("888")
    // let data = JSON.parse(r)
    // for (let c in data) {
    //   $('#showdata').append("<option value=" + data[c].grcode + " >" + data[c].grcode + "</option>")
	//   $('#showdata20').append("<option value=" + data[c].grcode + " >" + data[c].supcode + "</option>")
	//   $('#datedata').append("<option value=" + data[c].grcode + " >" + data[c].grdate + "</option>")
	//   $('#supname').val( data[c].prename +' '+ data[c].supname )
	  
    // }
//   });  
// });

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

var inputempcode = document.getElementById("tx_empcode");
var inputweight = document.getElementById("tx_unitweigt");
var str;

inputempcode.addEventListener("keypress", function (event) {
	if (event.key === "Enter") {

		if (inputweight.value > 0) {

			let data = response;
			// console.log(data['data'][0]['empcode'])
			$.post("https://tidmunzbuffet.com/api_app/barcode/add_barcode.php", { empcode: data['data'][0]['empcode'], unit_weight: inputweight.value, product_id: $("#selproduct").val(), create_date: data['data'][0]['create_date'] }, function (response2) {

				let r2 = JSON.parse(response2)

				if (r2.status === 1) {
					count++;
					str = '';
					str += '<tr id="' + count + '"><td>' + data['data'][0]['display_date'] + '</td><td>' + data['data'][0]['empcode'] + '</td><td>' + data['data'][0]['firstname'] + ' ' + data['data'][0]['lastname'] + '</td><td>' + inputweight.value + '</td><td>0</td><td><button class="btn btn-secondary" onclick="PrintBarcode(' + r2.id + ');"> Edit</button></td>';
					str += '</tr>';

					$("#tbmain").prepend(str);
					const elmnt = document.querySelector("table tr:last-child");
					elmnt.scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });
					document.getElementById('txtresult').innerHTML = 'บันทึกข้อมูลรหัส ' + data['data'][0]['empcode'] + ' สำเร็จ '
					document.getElementById('txtresult').style.color = "#4DBE05";
				}
				else {
					document.getElementById('txtresult').innerHTML = r2.message
					document.getElementById('txtresult').style.color = "red";
				}
			}).fail(function (error) {

				$('#txtresult').text('อินเตอร์เน็ตมีปัญหา เชื่อมต่อไม่ได้')
			});



		} else {

			document.getElementById('txtresult').innerHTML = 'กรุณาชั่งสินค้าก่อน ยิง Barcode'
			document.getElementById('txtresult').style.color = "red";

		}
		inputempcode.value = null;
		inputempcode.click();
		event.preventDefault();
	}
});