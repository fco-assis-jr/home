<div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="sampleTable" wire:ignore.self>
            <thead>
            <tr class="text-uppercase text-center">
                <th class="text-center">CODSUG</th>
                <th class="text-center">{{ $tabela }}</th>
                <th class="text-center">DATA CRIAÇÃO</th>
                <th class="text-center">FILIAL</th>
                <th class="text-center">QT ITENS</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($dados_filtrados as $index => $item)
                <tr class="text-uppercase text-center align-middle cursor-pointer" wire:key="{{ $index }}">
                    <td class="text-center">{{ $item->codsug }}</td>
                    <td class="text-center">{{ $item->tabela }}</td>
                    <td class="text-center">{{ $item->data }}</td>
                    <td class="text-center">{{ $item->codfilial }}</td>
                    <td class="text-center">{{ $item->quantidade }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
