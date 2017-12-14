import { Injectable } from '@angular/core';
//import * as FileSaver from 'file-saver';

export class UtilS {
	/*JSONToCSV(JSONData, ReportTitle, ShowLabel, listac) {
	    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;    
	    var CSV = '';    
	    CSV += ReportTitle + '\r\n\n';
	    if (ShowLabel) {
	        var row = "";
	        for (var index in listac) {
	            row += index + ',';
	        }
	        row = row.slice(0, -1);
	        CSV += row + '\r\n';
	    }
	    for (var i = 0; i < arrData.length; i++) {
	        var row = "";
	        for (var index in listac) {
	        	console.log("["+i+","+index+"]");
	            row += '"' + arrData[i][index] + '",';
	        }
	        row.slice(0, row.length - 1);
	        CSV += row + '\r\n';
	    }
	    if (CSV == '') {        
	        alert("Invalid data");
	        return;
	    } 
	    let blob = new Blob([CSV], {type: "text/csv;charset=utf-8"});
	    FileSaver.saveAs(blob, "reporte.xls");  
	}*/
	
	fechaACadena(f):string{
      return f.year+'-'+(f.month<=9?("0"+f.month):f.month)+'-'+(f.day<=9?("0"+f.day):f.day);
    }
    DateACadena(f:Date):string{
      return f.getFullYear()+'-'+
      	(f.getMonth()<9?("0"+(f.getMonth()+1)):(f.getMonth()+1))+"-"+
      	(f.getDate()<=9?("0"+f.getDate()):f.getDate());
    }
    /**
	 * Convierte una cadena a un objeto Date
	 * @param {string} f 
	 * @return {Date} fecha
	 */
    CadenaADate(f:string){
    	
    	var anios:number=+(f.substring(6,10)) ;
    	var mes:number=+(f.substring(3,5));
    	var dia:number=+(f.substring(0,2));
    	return new Date(anios,mes-1,dia);
    }
    horaACadena(h){
    	return (h.hour<=9?("0"+h.hour):h.hour)+':'+(h.minute<=9?("0"+h.minute):h.minute)
    }

    binACodigo(b:boolean){
    	return b?1:0
    }

    codigoABin(c:number){
    	return (c==1);
    }

    obtElementoPorNombre(arr:any, campo:string, valor:any){
    	for(let a of arr){
    		if(a[campo]==valor) return a;
    	}
    	return null;
    }

    qto(q:any) {
			//console.log('1-->');
		    var col:any, i:any, r:any, _i:any, _len:any, _ref:any, _ref2:any, _results:any;
		    _results = [];
		    for (i = 0, _ref = q.DATA.length; 0 <= _ref ? i < _ref : i > _ref; 0 <= _ref ? i++ : i--) {		    	
		      r = {};
		      _ref2 = q.COLUMNS;
		      for (_i = 0, _len = _ref2.length; _i < _len; _i++) {
		        col = _ref2[_i];
		        r[col.toLowerCase()] = q.DATA[i][_i];
		      }
		      _results.push(r);
		    }
		    return _results;
		 }
}
