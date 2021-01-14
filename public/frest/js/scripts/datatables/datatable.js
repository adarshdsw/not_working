$(document).ready(function () {
    $(".zero-configuration").DataTable();
    var a = $(".row-grouping").DataTable({
        columnDefs: [{ visible: !1, targets: 2 }],
        order: [[2, "asc"]],
        displayLength: 10,
        drawCallback: function (a) {
            var o = this.api(),
                t = o.rows({ page: "current" }).nodes(),
                r = null;
            o.column(2, { page: "current" })
                .data()
                .each(function (a, o) {
                    r !== a &&
                        ($(t)
                            .eq(o)
                            .before('<tr class="group"><td colspan="5">' + a + "</td></tr>"),
                        (r = a));
                });
        },
    });
    $(".row-grouping tbody").on("click", "tr.group", function () {
        var o = a.order()[0];
        2 === o[0] && "asc" === o[1] ? a.order([2, "desc"]).draw() : a.order([2, "asc"]).draw();
    }),
        $(".complex-headers").DataTable();
    var o = $(".add-rows").DataTable(),
        t = 2;
    $("#addRow").on("click", function () {
        o.row.add([t + ".1", t + ".2", t + ".3", t + ".4", t + ".5"]).draw(!1), t++;
    }),
        $(".dataex-html5-selectors").DataTable({
            dom: "Bfrtip",
            buttons: [
                { extend: "copyHtml5", exportOptions: { columns: [0, ":visible"] } },
                { extend: "pdfHtml5", exportOptions: { columns: ":visible" } },
                {
                    text: "JSON",
                    action: function (a, o, t, r) {
                        var e = o.buttons.exportData();
                        $.fn.dataTable.fileSave(new Blob([JSON.stringify(e)]), "Export.json");
                    },
                },
                { extend: "print", exportOptions: { columns: ":visible" } },
            ],
        }),
        $(".scroll-horizontal-vertical").DataTable({ scrollY: 200, scrollX: !0 });
});
