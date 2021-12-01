Olá, segue cotação<br><br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-plain">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title mt-0"> Histórico</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class=" text-primary">
                                    <tr style="text-align: center;font-weight: bold;">
                                        <th>Moeda origem</th>
                                        <th>Moeda destino</th>
                                        <th>Valor Conversão</th>
                                        <th>Forma Pagamento</th>
                                        <th>Valor Unitário</th>
                                        <th>Taxa pagamento</th>
                                        <th>Taxa conversão</th>
                                        <th>Valor sem taxa</th>
                                        <th>Valor comprado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="text-align: center;">
                                        <td>{{ $cotacao["moedaOrigem"]}}</td>
                                        <td>{{ $cotacao["moedaDestino"]}}</td>
                                        <td>{{ $cotacao["moedaOrigem"].' '. $cotacao["valorConversao"]}}</td>
                                        <td>{{ $cotacao["formaPag"]}}</td>
                                        <td>{{ $cotacao["moedaDestino"] .' '. $cotacao["valorUnitarioMoedaDestino"]}}</td>
                                        <td>{{ $cotacao["moedaOrigem"] .' '. $cotacao["taxaPagamento"]}}</td>
                                        <td>{{ $cotacao["moedaOrigem"] .' '.  $cotacao["taxaConversao"]}}</td>
                                        <td>{{ $cotacao["moedaOrigem"] .' '. $cotacao["valorSemTaxa"]}}</td>
                                        <td>{{ $cotacao["moedaDestino"] .' '. $cotacao["valorComprado"]}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>