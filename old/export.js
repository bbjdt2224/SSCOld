function export()
 {
    var pdf = new jsPDF('p', 'pt', 'letter'),
    source = $('#tablediv')[0],
    margins = {
        top: 40,
        bottom: 40,
        left: 40,
        right: 40,
        width: 522
    };

pdf.fromHTML(
        source,
        margins.left,
        margins.top,
        {
        'width': margins.width 
        },
        function (dispose) {
            pdf.save('Test.pdf');
        },
        margins
   );
};