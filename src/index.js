var inputdncode = document.getElementById("numdncode");
inputdncode.addEventListener("keypress", function (event) {
  if (event.key === "Enter") {
	if (inputdncode.value == 8859126000508) {
	  $.post("https://tidmunzbuffet.com/api_app/gr/get_gr.php", function (grdetail1) {
		let result = JSON.parse(grdetail1)
		$('#tbmain tbody').empty();
	
		for (let i in result) {
		  tb = '';
		  tb += '<tr id=" "><td>' + result[0].grdate + '</td><td>' + result[0].supcode + '</td><td>' + result[0].supname + '</td><td>' + result[i].price + '</td><td>' + result[i].totalprice + '</td>';
		  tb += '</tr>';
		  $(tb).appendTo("#tbmain");
		}
	  })
	}
	event.preventDefault();
	inputdncode.value = null;

  }
});
