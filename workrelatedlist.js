$(document).ready(function () {

    $.fn.dataTable.ext.legacy.ajax = true;

    var url = Routing.generate(target_route);

    console.log(url);
    var oTable = $('#objects_list').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": url,
        "columnDefs": [
            {
                "render": function (data, type, row) {
                    return '<b>' + data + '</b>';
                },
                "targets": 2
            },
            {
                "render": function (data, type, row) {
                    return '<i>' + data + '<i>';
                },
                "targets": [1, 2]

            },
            {
                "mData": null,
                "sClass": "center",
                "targets": 3,
                "render": function (row) {
                    return '<button class="btn btn-block">' + row[0] + '</button>';
                }
            }
        ],
        "order": [[0, "asc"]],
        "searchDelay": 500

    });


});
