
function createBrowserWindow() {

	const ipc = require("electron").ipcRenderer;
	ipc.send('message:loginShow');

}
function createBrowserWindow2() {

	const ipc = require("electron").ipcRenderer;
	ipc.send('message:loginShow2');

}
var inputdncode = document.getElementById("numdncode");
inputdncode.addEventListener("keypress", function (event) {
  if (event.key === "Enter") {
	if (inputdncode.value === 8859126000508) {
	  $.post("https://tidmunzbuffet.com/api_app/gr/get_gr.php", function (grdetail1) {
		let result = JSON.parse(grdetail1)
		$('#tbmain tbody').empty();
	
		for (let i in result) {
		  tb = '';
		  tb += '<tr id="' + (i + 1) + '"><td>' + result[i].grdate + '</td><td>' + result[i].supcode + '</td><td>' + result[i].supname + '</td><td>' + result[i].price + '</td><td>' + result[i].totalprice + '</td>';
		  tb += '</tr>';
		  $(tb).appendTo("#tbmain");
		}
	  })
	}
	event.preventDefault();
	inputdncode.value = null;

  }
});
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




// var inputempcode = document.getElementById("tx_empcode");
// var str;

// inputempcode.addEventListener("keypress", function (event) {
// 	if (event.key === "Enter") {

// 		if (inputweight.value > 0) {

// 			let data = response;
// 			// console.log(data['data'][0]['empcode'])
// 			$.post("https://tidmunzbuffet.com/api_app/barcode/add_barcode.php", { empcode: data['data'][0]['empcode'], unit_weight: inputweight.value, product_id: $("#selproduct").val(), create_date: data['data'][0]['create_date'] }, function (response2) {

// 				let r2 = JSON.parse(response2)

// 				if (r2.status === 1) {
// 					count++;
// 					str = '';
// 					str += '<tr id="' + count + '"><td>' + data['data'][0]['display_date'] + '</td><td>' + data['data'][0]['empcode'] + '</td><td>' + data['data'][0]['firstname'] + ' ' + data['data'][0]['lastname'] + '</td><td>' + inputweight.value + '</td><td>0</td><td><button class="btn btn-secondary" onclick="PrintBarcode(' + r2.id + ');"> Edit</button></td>';
// 					str += '</tr>';

// 					$("#tbmain").prepend(str);
// 					const elmnt = document.querySelector("table tr:last-child");
// 					elmnt.scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });
// 					document.getElementById('txtresult').innerHTML = 'บันทึกข้อมูลรหัส ' + data['data'][0]['empcode'] + ' สำเร็จ '
// 					document.getElementById('txtresult').style.color = "#4DBE05";
// 				}
// 				else {
// 					document.getElementById('txtresult').innerHTML = r2.message
// 					document.getElementById('txtresult').style.color = "red";
// 				}
// 			}).fail(function (error) {

// 				$('#txtresult').text('อินเตอร์เน็ตมีปัญหา เชื่อมต่อไม่ได้')
// 			});



// 		} else {

// 			document.getElementById('txtresult').innerHTML = 'กรุณาชั่งสินค้าก่อน ยิง Barcode'
// 			document.getElementById('txtresult').style.color = "red";

// 		}
// 		inputempcode.value = null;
// 		inputempcode.click();
// 		event.preventDefault();
// 	}
// });