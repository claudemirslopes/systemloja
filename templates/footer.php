</div>
            </div>
        </div>
    </div>    <!-- SB Admin 2 JS e FontAwesome -->    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <style>
        /* DataTables: botões à esquerda, filtro à direita na mesma linha */
        .dataTables_wrapper .dt-buttons {
            float: left;
            margin-right: 1em;
            margin-bottom: 0;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 0;
            margin-top: 0;
        }
        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.5em;
        }
        .dataTables_wrapper .dataTables_filter label input[type="search"] {
            margin-left: 0.5em;
            border: 1px solid #d1d5db !important; /* borda discreta */
            border-radius: 6px;
            box-shadow: none;
            background: #fff;
            padding: 0.35em 0.7em;
            font-size: 1em;
            transition: border-color 0.2s;
        }
        .dataTables_wrapper .dataTables_filter label input[type="search"]:focus {
            border-color: #6366f1 !important;
            outline: none;
        }
        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 0;
            margin-top: 0;
        }
        .dataTables_wrapper .dataTables_info {
            float: left;
            margin-top: 0.5em;
        }
        .dataTables_wrapper .dataTables_paginate {
            float: right;
            margin-top: 0.5em;
        }
        /* Corrige espaçamento dos botões */
        .dt-buttons .btn, .dt-buttons .buttons-copy, .dt-buttons .buttons-excel, .dt-buttons .buttons-pdf, .dt-buttons .buttons-print {
            margin-right: 0.3em;
        }
        /* Corrige ícones dos botões DataTables */
        .dt-button i.fa {
            margin-right: 4px;
        }
        /* Corrige paginação para exibir ícones */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.8em;
            border-radius: 4px;
            border: 1px solid #e0e7ff;
            background: #f9fafb;
            color: #6366f1 !important;
            margin: 0 2px;
            font-weight: 600;
            transition: background 0.2s;
            cursor: pointer;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #6366f1 !important;
            color: #fff !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e0e7ff !important;
            color: #6366f1 !important;
        }
        /* Ajuste responsivo */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dt-buttons {
                float: none;
                text-align: left;
                width: 100%;
                margin-bottom: 0.5em;
            }
            .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: left;
                width: 100%;
            }
        }
    </style>
    <!-- Corrige ícones de paginação do DataTables -->
    <script>
    if (window.jQuery && $.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                paginate: {
                    previous: '<i class="fa fa-chevron-left"></i>',
                    next: '<i class="fa fa-chevron-right"></i>'
                }
            }
        });
    }
    </script>
    <script>
window.addEventListener('DOMContentLoaded', function() {
  // Teste jQuery
  if (window.jQuery) {
    document.getElementById('jquery-test').innerText = 'jQuery OK';
  } else {
    document.getElementById('jquery-test').innerText = 'jQuery FALHOU';
  }
  // Teste Modal Bootstrap 5
  document.getElementById('abrirModalTeste').onclick = function() {
    var myModal = new bootstrap.Modal(document.getElementById('modalTeste'));
    myModal.show();
  };
});
    </script>
    <script src="/systemloja/assets/js/scripts.js"></script>
</body>
</html>
