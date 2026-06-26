$(document).ready(function(){
    $("#export_button").click(function(){

        var wb = XLSX.utils.book_new();
        wb.Props = {
                Title: "Registros",
        };
    
        wb.SheetNames.push("registros");
        var ws_data = [['hello' , 'world']];
        var ws = XLSX.utils.aoa_to_sheet(ws_data);
        wb.Sheets["Test Sheet"] = ws;
        var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});
        function s2ab(s) {
    
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
                
        }
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'test.xlsx');
    });

})