/*
import './bootstrap.js';
import './jquery-3.7.0.min.js'
import './bootstrap.bundle.min.js'
import './plugins/jquery.dataTables.min.js';
import './main.js';
*/
import './jquery.min.js';
import './jquery-3.7.0.min.js';
import './bootstrap.js';
import './bootstrap.min.js';
import './main.js';
import './plugins/dataTables.min.js';


$('#sampleTable').DataTable({
    language: {
        "sEmptyTable": "Nenhum dado disponível na tabela",
        "sInfo": "Mostrando _START_ até _END_ de _TOTAL_ entradas",
        "sInfoEmpty": "Mostrando 0 até 0 de 0 entradas",
        "sInfoFiltered": "(filtrado de _MAX_ entradas no total)",
        "sInfoPostFix": "",
        "sInfoThousands": ".",
        "sLengthMenu": "Mostrar _MENU_ entradas",
        "sLoadingRecords": "Carregando...",
        "sProcessing": "Processando...",
        "sSearch": "Buscar:",
        "sZeroRecords": "Nenhum registro encontrado",
        "oPaginate": {
            "sNext": "Próximo",
            "sPrevious": "Anterior"
        }
    }
});
