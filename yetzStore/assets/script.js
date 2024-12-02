document.addEventListener('DOMContentLoaded', function () {
  // Função para atualizar o carrinho
  function atualizarCarrinho() {
      const form = document.querySelector('#form-carrinho');
      const data = new FormData(form);

      fetch('/yetzStore/atualizar_carrinho.php', {
          method: 'POST',
          body: data,
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              // Atualiza a quantidade de itens no carrinho
              document.querySelector('.badge').textContent = data.total_items;
              alert('Carrinho atualizado!');
          } else {
              alert('Erro ao atualizar o carrinho.');
          }
      })
      .catch(error => {
          console.error('Erro:', error);
      });
  }

  // Adicionar evento de submit no formulário
  document.querySelector('#form-carrinho').addEventListener('submit', function (event) {
      event.preventDefault();
      atualizarCarrinho();
  });
});
