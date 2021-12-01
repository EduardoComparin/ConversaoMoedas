@extends('layouts.app', ['activePage' => 'dashboard', 'titlePage' => __('Cotação')])

@section('content')
<div class="alert alert-danger" id="danger-alert" style="display: none;text-align: center;">
  <button type="button" class="close" data-dismiss="alert">x</button>
  <span id="msgDangerAlert"></span>
</div>
<div class="content">
  <div class="container-fluid" style="height: auto;">

    <div class="row align-items-center">
      <div class="col-lg-6 col-md-8 col-sm-12 ml-auto mr-auto">
        <form id="formCotacao">
          @csrf
          <div class="card card-login card-hidden mb-3">
            <div class="card-body">
              <div class="input-group" style="display: none;">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    Moeda origem
                  </span>
                </div>
                <select autocomplete="off" class="form-control" name="moeda_entrada" id="moeda_entrada"></select>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    Converter para
                  </span>
                </div>
                <select autocomplete="off" class="form-control" name="moeda_saida" id="moeda_saida"></select>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    Valor conversão
                  </span>
                </div>
                <input autocomplete="off" value='5.000,00' type="text" name="valor" id="valor" class="form-control" placeholder="{{ __('valor...') }}" required>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    Pagamento
                  </span>
                </div>
                <select autocomplete="off" class="form-control" name="formaPag" id="formaPag">
                  <option value="blt">Boleto</option>
                  <option value="card">Cartão de Crédito</option>
                </select>
              </div>
            </div>
            <div class="card-footer justify-content-center">
              <button type="submit" class="btn btn-primary btn-link btn-lg">{{ __('Cotar') }}</button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<div class="modal fade bd-example-modal-lg" id="modalResultado" tabindex="-1" role="dialog" aria-labelledby="modalResultado" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalResultado">Resultado da cotação</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="vertical-align: middle; text-align: left;">
        <pre>
            Moeda de origem: <span id="spanMoedaOrigem"></span>
            Moeda de destino: <span id="spanMoedaDestino"></span>
            Valor para conversão em <span id="spanValorConversao"></span>
            Forma de pagamento: <span id="spanFormaPag"></span>
            Valor para conversão em <span id="spanValorDestinoConversao"></span> 
            Valor comprado em <span id="spanValorComprado"></span>
            Taxa de pagamento em <span id="spanTaxaPagamento"></span>
            Taxa de conversão em <span id="spanTaxaconversao"></span>
            Valor descontando taxas em <span id="spanValorSemTaxa"></span>
          </pre>
      </div>
    </div>
  </div>
</div>

@endsection

@push('js')
<script type="text/javascript">
  const taxaBol = 1.37 / 100;
  const taxaCard = 7.73 / 100;
  const taxaMenor = 2 / 100;
  const taxaMaior = 1 / 100;

  var moedasUniq = null;
  var moedaDefault = '';
  var moedasConversao = null;

  function buscarMoedasUnicas() {
    /**
     * Buscando as moedas unicas para popular as caixas de  seleção
     */
    $.ajax({
      url: "https://economia.awesomeapi.com.br/json/available/uniq",
      type: 'GET',
      success: function(json_data) {
        moedasUniq = Object.keys(json_data).map((key) => [key, json_data[key]]);
        /** Add somente a BRL */
        moedaDefault = moedasUniq.find(x => x[0] == 'BRL');
        if (moedaDefault) {
          $('#moeda_entrada').append('<option value="' + moedaDefault[0] + '">' +
            moedaDefault[1] + '</option>');
        }

        buscarMoedasConversao();
      }
    });

  }

  /** 
   * criando map para popular a caixa de moeda destino com as moedas que possuem corversao 
   */
  function buscarMoedasConversao() {

    $.ajax({
      url: "https://economia.awesomeapi.com.br/json/available",
      type: 'GET',
      async: true,
      success: function(json_data) {
        moedasConversao = Object.keys(json_data).map((key) => [key, json_data[key]]);

        // Ordenando a lista
        moedasConversao.sort(function(a, b) {
          if (a[1] > b[1]) {
            return 1;
          }
          if (a[1] < b[1]) {
            return -1;
          }
          return 0;
        });

        // Add somente as moedas que possuem corversao para a moeda default escolhida
        moedasConversao.map(function(moeda) {
          if (moeda[0].includes("-" + moedaDefault[0])) {
            var name = moeda[0].split('-');
            var descricao = moeda[1].split('/');

            $('#moeda_saida').append('<option value="' + name[0] + '">' + descricao[0] +
              '</option>');
          }
        });

        $('#moeda_saida option[value="' + moedaDefault[0] + '"]').attr("disabled", 'disabled');
        $('#moeda_saida option[value="USD"]').attr("selected", true);
      }
    });
  }

  /**
   * Seta e mostra o modal
   */
  function setCamposTemplate(cotacao) {

    $('#spanMoedaOrigem').text(cotacao.moedaOrigem);
    $('#spanMoedaDestino').text(cotacao.moedaDestino);
    $('#spanValorConversao').text(cotacao.valorConversao);
    $('#spanFormaPag').text(cotacao.formaPag);
    $('#spanValorDestinoConversao').text(cotacao.valorUnitarioMoedaDestino);
    $('#spanValorComprado').text(cotacao.valorComprado);
    $('#spanTaxaPagamento').text(cotacao.taxaPagamento);
    $('#spanTaxaconversao').text(cotacao.taxaConversao);
    $('#spanValorSemTaxa').text(cotacao.valorSemTaxa);

    $('#modalResultado').modal('show');
  }


  function salvaRegistro(retorno) {
    $.ajax({
      url: "/salvarDadosCotacao",
      type: "POST",
      data: retorno,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        $('.retorno_msg_cotacao').text(response.success);
       // $('.modal-body').html(response);
       // $('#modalResultado').modal('show');
      },
      error: function(error) {
        console.log(error);
      }
    });
  }


  /**
   * Obtendo cotação
   */
  function efetuarCotacao(moedaEntrada, moedaSaida, valor, formaPag) {
    $.ajax({
      url: "https://economia.awesomeapi.com.br/json/last/" + moedaSaida + "-" + moedaEntrada,
      type: 'GET',
      success: function(json_data) {

        var chave = moedaSaida + moedaEntrada;
        var chaveRet = Object.keys(json_data)[0];
        var valoresRet = Object.values(json_data)[0]

        let taxaConversao = 0;
        let taxaPagamento = 0;
        let valorSemTaxa = 0;
        let moedaComprada = 0;
        let valorMoeda = valoresRet.bid;
        let casaDecimal = 2;
        let nomesMoedas = valoresRet.name.split('/');

        if (chave == chaveRet) {
          /**
           *  Aplicando a taxa da forma de pagamento
           */
          if (formaPag === "blt") {
            taxaPagamento = parseFloat(valor * taxaBol).toFixed(2);
            formaPag = 'Boleto';
          } else {
            taxaPagamento = parseFloat(valor * taxaCard).toFixed(2);
            formaPag = 'Cartão de crédito';
          }

          /**
           * Aplicando a taxa de conversão
           */
          if (valor < 2700.00) {
            taxaConversao = parseFloat(valor * taxaMenor).toFixed(2);
          } else if (valor > 4000.00) {
            taxaConversao = parseFloat(valor * taxaMaior).toFixed(2);
          }

          valorSemTaxa = parseFloat(valor - taxaConversao - taxaPagamento).toFixed(2);

          switch (moedaSaida) {
            case 'BTC':
              // Cotação do btc esta vindo errada "323.113" onde deveria ser "323113.00"
              valorMoeda = parseFloat(valorMoeda) * 1000;
              casaDecimal = 7;
              break;
          }

          moedaComprada = parseFloat(valorSemTaxa / valorMoeda).toFixed(casaDecimal);

          var retorno = {
            moedaOrigem: valoresRet.codein,
            moedaDestino: valoresRet.code,
            valorConversao: formataValorBr(valor),
            formaPag: formaPag,
            valorUnitarioMoedaDestino: formataValorBr(valorMoeda),
            valorComprado: formataValorBr(moedaComprada, casaDecimal),
            taxaPagamento: formataValorBr(taxaPagamento),
            taxaConversao: formataValorBr(taxaConversao),
            valorSemTaxa: formataValorBr(valorSemTaxa),
            nomeMoedaOrigem: nomesMoedas[1],
            nomeMoedaDestino: nomesMoedas[0],
          };

          setCamposTemplate(retorno);

          salvaRegistro(retorno);

        } else {
          msgAlert('Moeda não encontrada.');
        }
      },
      error: function(json) {
        msgAlert(json.responseJSON.message);
        $('#moeda_saida option[value="' + moedaSaida + '"]').attr("disabled",
          'disabled');

      },
    });

  }

  function formataValorBr(valor, casaDecimal = 2) {
    var number = parseFloat(valor).toFixed(casaDecimal);
    var valorRet = (new Intl.NumberFormat('pt-BR', {
      minimumFractionDigits: casaDecimal
    }).format(number));
    return valorRet;
  }

  function msgAlert(msg) {
    $("#danger-alert").fadeTo(5000, 500).slideUp(500, function() {
      $("#danger-alert").slideUp(500);
    });
    $("#msgDangerAlert").text(msg.toUpperCase());
  }

  buscarMoedasUnicas();


  $(document).ready(function() {
  
    /** Tratamentos input valor */
    $('#valor').on('blur', function() {
      let valor = this.value.replaceAll('.', '').replaceAll(',', '.');
      var number = parseFloat((valor / 100)).toFixed(2);
      this.value = (new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: 2
      }).format(number));
    });

    // apaga conteudo ao entrar no input
    $('#valor').on('focus', function() {
      this.value = '';
    });
    
    // remove as letras no input
    $('#valor').keyup(function() {
      this.value = this.value.replace(/\D+/g, '');
    });

    /** Fazer a Cotação */
    $('#formCotacao').on('submit', function(e) {
      e.preventDefault();

      let moedaEntrada = $('#moeda_entrada').val();
      let moedaSaida = $('#moeda_saida').val();
      let valor = $('#valor').val().replaceAll('.', '').replaceAll(',', '.');
      let formaPag = $('#formaPag').val();

      if (valor > 900.00 && valor < 900000.00) {
        var cotacao = efetuarCotacao(moedaEntrada, moedaSaida, valor, formaPag);
      } else {
        msgAlert('Valor deve ser maior que R$ 900,00 e menor que R$ 900.000,00')
      }
    });
  });
</script>

@endpush