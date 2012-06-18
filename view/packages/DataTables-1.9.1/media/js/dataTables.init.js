$(document).ready( initDataTables );

function initDataTables(){
    $('.sortable').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers" 
    });
}