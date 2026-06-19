var lang_es = {
	months:[
		"Enero",
		"Febrero",
		"Marzo",
		"Abril",
		"Mayo",
		"Junio",
		"Julio",
		"Agosto",
		"Septiembre",
		"Octubre",
		"Noviembre",
		"Diciembre"
	],
	weekdays: [
		"Domingo",
	  	"Lunes",
	  	"Martes",
	  	"Miércoles",
	  	"Jueves",
	  	"Viernes",
		"Sábado"
	],
	downloadJPEG: "Descargar Imagen JPEG",
	downloadPDF: "Descargar Documento PDF",
	downloadPNG: "Descargar Imagen PNG",
	downloadSVG : "Descargar Imagen SVG",
	printChart: "Imprimir chart",
	resetZoom: "Resetear zoom",
	resetZoomTitle: "Resetear zoom",
	getWeekDays: function(){
		return this.weekdays;
	},
	getMonths: function(){
		return this.months;
	},
	getShortWeekDays: function(){
		return this.weekdays.map(function(day){
			return day.substring(0,3);
		});
	},
	getShortMonths: function(){
		return this.months.map(function(month){
			return month.substring(0,3);
		});
	}
}

Highcharts.setOptions({
	lang:{
		months : lang_es.getMonths(),
		weekdays: lang_es.getWeekDays(),
		shortWeekdays: lang_es.getShortWeekDays(),
		shortMonths: lang_es.getShortMonths(),
		downloadJPEG: lang_es.downloadJPEG,
		downloadPDF: lang_es.downloadPDF,
		downloadPNG: lang_es.downloadPNG,
		downloadSVG : lang_es.downloadSVG,
		printChart: lang_es.printChart,
		resetZoom: lang_es.resetZoom,
		resetZoomTitle: lang_es.resetZoomTitle,
        
	}
});