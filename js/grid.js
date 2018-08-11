// grid.js
function grid(obj, e) {
	switch(e.keyCode) {
		case 37: // flecha izquierda
			$(obj).parent()
				.prev()
				.children()
				.focus();
			break;
		case 39: // flecha derecha
			$(obj).parent()
				.next()
				.children()
				.focus();
			break;
		case 40: // flecha abajo
			$(obj).parent()
				.parent()
				.next()
				.children("td")
				.children("input")
				.first()
				.focus();
			break;
		case 38: // flecha arriba
			$(obj).parent()
				.parent()
				.prev()
				.children("td")
				.children()
				.first()
				.focus();
			break;
	}
}