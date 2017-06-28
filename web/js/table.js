$(document).ready(function() {
    var table = $('#data-table').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.15/i18n/French.json"
        },
        responsive: true
    });
});