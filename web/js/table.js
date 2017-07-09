$(document).ready(function() {
    var table = $('#data-table').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        	language: {
            	url: urlLg
        	},
        responsive: true
    });
});