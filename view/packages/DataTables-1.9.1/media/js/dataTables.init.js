$(document).ready( initDataTables );

var dataTableOptions = {
        "bJQueryUI": true,
        "sPaginationType": "full_numbers" 
};

function initDataTables(){
    $('.sortable').dataTable(dataTableOptions);
}