
const { ipcRenderer } = require("electron");

$(document).mousemove(function (event) {
	$("#numdncode").focus();
});

ipcRenderer.on("got-access-token", (event, accessToken) => {
//   data = accessToken.split(",");
  let grcode = accessToken;
  $.post("https://tidmunzbuffet.com/api_app/gr/getsup_gr.php", { grcode : grcode }, function (grhead) {
  let socode = accessToken;

  $.post("https://tidmunzbuffet.com/api_app/so/getsup_so.php", { socode : socode }, function (sohead) {
	// console.log(grhead);
    let result = JSON.parse(sohead)
	$('#socode').val(socode)
	$('#cuscode').val(result[0].cuscode)
	$('#sodate').val(result[0].sodate)
	$('#cusname').val(result[0].cusname	)
  
  });

  $.post("https://tidmunzbuffet.com/api_app/so/getsup_sodetail.php", { socode : socode }, function (sodetail) {
	// console.log(grdetail);
    let result = JSON.parse(sodetail)
    $('#tbmain tbody').empty();

    for (let i in result) {
		let count = (parseInt(i)+1)
      tb = '';
      tb += '<tr id="' + (i + 1) + '"><td>' + count + '</td><td>' + result[i].stcode + '</td><td>' + result[i].stname + '</td><td>' + result[i].qty + '</td><td>' + result[i].delamount + '</td>';
      tb += '</tr>';
      $(tb).appendTo("#tbmain");
    }

  }).fail(function (error) {

    $('#txtresult').text('อินเตอร์เน็ตมีปัญหา เชื่อมต่อไม่ได้') 
  });



});

function PrintBarcode(data) {

	const electron = require('electron')
// Importing BrowserWindow from Main 
const BrowserWindow = electron.remote.BrowserWindow;

// var current = document.getElementById('current');
var options = {
	silent: false,
	printBackground: true,
	color: false,
	margin: {
		marginType: 'printableArea'
	},
	landscape: false,
	pagesPerSheet: 1,
	collate: false,
	copies: 1,
	header: 'Header of the Page',
	footer: 'Footer of the Page'
}

	// alert('test')
	let win = BrowserWindow.getFocusedWindow();
	// let win = BrowserWindow.getAllWindows()[0]; 

	win.webContents.print(options, (success, failureReason) => {
		if (!success) console.log(failureReason);

		console.log('Print Initiated');
	});
}