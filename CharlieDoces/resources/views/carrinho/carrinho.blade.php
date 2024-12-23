<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Carrinho</title>
    <link rel="stylesheet" href="{{ asset('css/carrinho.css') }}">
    <!-- Adicione o CSS do Bootstrap para estilização e modal -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    @include('profile.partials.header', ['categorias' => \App\Models\Categoria::all()])

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


    <div class="carrinho-container">
        <h1>Meu Carrinho</h1>

        @if($items->isEmpty())
            <p>Seu carrinho está vazio. Continue comprando!</p>
        @else
            <table class="carrinho-tabela">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>
                                <img src="{{ $item->produto->produto_imagens->first()->IMAGEM_URL }}" alt="{{ $item->produto->PRODUTO_NOME }}" width="50">
                                {{ $item->produto->PRODUTO_NOME }}
                            </td>
                            <td>
                                <form action="{{ route('carrinho.atualizar', $item->PRODUTO_ID) }}" method="POST">
                                    @csrf
                                    <input type="number" name="ITEM_QTD" value="{{ $item->ITEM_QTD }}" min="1">
                                    <button type="submit">Atualizar</button>
                                </form>
                            </td>
                            <td>R$ {{ number_format($item->produto->PRODUTO_PRECO - $item->produto->PRODUTO_DESCONTO, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format(($item->produto->PRODUTO_PRECO - $item->produto->PRODUTO_DESCONTO) * $item->ITEM_QTD, 2, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('carrinho.remover', $item->PRODUTO_ID) }}" method="POST">
                                    @csrf
                                    <button type="submit">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="carrinho-total">
                <p>Total: R$ {{ number_format($items->sum(function($item) {
                    return ($item->produto->PRODUTO_PRECO - $item->produto->PRODUTO_DESCONTO) * $item->ITEM_QTD;
                }), 2, ',', '.') }}</p>
                <!-- Botão que abre o modal -->
                <button class="botao-finalizar" data-toggle="modal" data-target="#finalizarCompraModal">Finalizar Compra</button>
            </div>
        @endif
    </div>

    @include('profile.partials.footer')

    <!-- Modal -->
    <div class="modal fade" id="finalizarCompraModal" tabindex="-1" role="dialog" aria-labelledby="finalizarCompraModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <form action="{{ route('pedido.finalizar') }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="finalizarCompraModalLabel">Finalizar Compra</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <!-- Lado Esquerdo: Produtos -->
                <div class="col-md-6">
                  <h5>Seus Produtos</h5>
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($items as $item)
                      <tr>
                        <td>{{ $item->produto->PRODUTO_NOME }}</td>
                        <td>{{ $item->ITEM_QTD }}</td>
                        <td>R$ {{ number_format(($item->produto->PRODUTO_PRECO - $item->produto->PRODUTO_DESCONTO) * $item->ITEM_QTD, 2, ',', '.') }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- Lado Direito: Resumo do Pedido -->
                <div class="col-md-6">
                  <h5>Resumo do Pedido</h5>
                  <div class="endereco">
                    <h6>Endereço de Entrega</h6>
                    @php
                      $endereco = Auth::user()->endereco;
                    @endphp
                    @if($endereco)
                      <p>
                        {{ $endereco->ENDERECO_LOGRADOURO }}, {{ $endereco->ENDERECO_NUMERO }}<br>
                        {{ $endereco->ENDERECO_COMPLEMENTO }}
                      </p>
                    @else
                      <p>Endereço não cadastrado.</p>
                    @endif
                  </div>
                  <div class="pagamento">
                    <h6>Forma de Pagamento</h6>
                    <select name="forma_pagamento" class="form-control" required>
                      <option value="ficticio">Fictício</option>
                    </select>
                  </div>
                  <div class="total">
                    <h6>Total: R$ {{ number_format($items->sum(function($item) {
                        return ($item->produto->PRODUTO_PRECO - $item->produto->PRODUTO_DESCONTO) * $item->ITEM_QTD;
                    }), 2, ',', '.') }}</h6>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Concluir Compra</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Scripts necessários -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
