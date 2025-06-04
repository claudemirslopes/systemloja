document.addEventListener('DOMContentLoaded', function() {
    if (window.jQuery && $("table.table").length) {
        $("table.table").each(function() {
            if (!$(this).hasClass('dataTable')) {
                $(this).DataTable({
                    order: [[0, 'desc']],
                    pageLength: 10,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            text: '<i class="fa fa-copy"></i> Copiar',
                            className: 'buttons-copy'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fa fa-file-excel"></i> Excel',
                            className: 'buttons-excel'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fa fa-file-pdf"></i> PDF',
                            className: 'buttons-pdf'
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i> Imprimir',
                            className: 'buttons-print'
                        }
                    ],
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json"
                    }
                });
            }
        });
    }
    // Máscara de telefone para campos com name="telefone" ou class="telefone"
    if (window.jQuery) {
        if (typeof $.fn.mask === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js';
            script.onload = function() {
                aplicarMascaraTelefone();
            };
            document.body.appendChild(script);
        } else {
            aplicarMascaraTelefone();
        }
        function aplicarMascaraTelefone() {
            $("input[name='telefone'], input.telefone").mask('(00) 00000-0000').on('blur', function() {
                var val = $(this).val();
                if (val.length === 15) { // (99) 99999-9999
                    $(this).mask('(00) 00000-0000');
                } else {
                    $(this).mask('(00) 0000-00009');
                }
            });
        }
    }
    // Máscara de valor monetário para campos de valor
    if (window.jQuery) {
        function aplicarMascaraValor() {
            $("input[name*='valor'], input.valor").each(function() {
                // Remove a máscara anterior antes de aplicar novamente
                if ($(this).data('mask-aplicada')) {
                    $(this).unmask();
                    $(this).removeData('mask-aplicada');
                }
                $(this).mask('000.000.000.000.000,00', {reverse: true});
                $(this).data('mask-aplicada', true);
            });
        }
        if (typeof $.fn.mask === 'undefined') {
            var script2 = document.createElement('script');
            script2.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js';
            script2.onload = function() {
                aplicarMascaraTelefone();
                aplicarMascaraValor();
            };
            document.body.appendChild(script2);
        } else {
            aplicarMascaraValor();
        }
    }
});
