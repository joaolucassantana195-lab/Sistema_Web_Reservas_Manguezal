const header = document.querySelector('header')

let configBanco = null;

//animacao header
if(window.innerWidth >= 750){
  window.document.addEventListener('scroll', () =>{
  if (window.scrollY <= 50){
    header.style = 'filter:opacity(1); border-bottom: 2px orange solid; transition:0.5s filter ease-in-out; background: linear-gradient(to bottom, rgb(0, 0, 0), #031723);'
  }else{
  header.style = 'filter:opacity(0); transition:0.5s filter ease-in-out; background: linear-gradient(to bottom, rgb(0, 0, 0), #031723);'
  }
})

header.addEventListener('mouseenter', () => {
  if (window.scrollY > 50){
     header.style = `background: linear-gradient(to bottom, rgb(0, 0, 0), #031723); border-bottom: 2px orange solid; filter:opacity(1); transition:0.5s filter ease-in-out;`
  }
})

header.addEventListener('mouseleave', () => {
  if(window.scrollY > 50){
    header.style = `filter:opacity(0); transition:0.5s filter ease-in-out;`
  }
  
})
}else{
  header.style = 'background: linear-gradient(to bottom, rgb(0, 0, 0), #031723); border-bottom: 2px orange solid;'
}

document.addEventListener('DOMContentLoaded', () => {
  // 1. Inicialização do FAQ (Accordion)
  const faqPerguntas = document.querySelectorAll('.faq-pergunta');

  faqPerguntas.forEach(pergunta => {
    pergunta.addEventListener('click', () => {
      const faqItem = pergunta.parentElement;
      const icone = pergunta.querySelector('.faq-icone');

      // Fecha outros itens abertos
      document.querySelectorAll('.faq-item').forEach(item => {
        if (item !== faqItem) {
          item.classList.remove('ativo');
          const itemIcone = item.querySelector('.faq-icone');
          if (itemIcone) itemIcone.textContent = '+';
        }
      });

      // Alterna o estado do item atual
      faqItem.classList.toggle('ativo');
      if (icone) {
        icone.textContent = faqItem.classList.contains('ativo') ? '-' : '+';
      }
    });
  });

  // 2. Carregamento dos dados dinâmicos da API
  carregarConfiguracoes();
});

function carregarConfiguracoes() {
  fetch('admin/api/api_configuracoes.php')
    .then(response => response.json())
    .then(config => {
      configBanco = config;
       let diasFuncionamento = config.dias_funcionamento;
      if (typeof diasFuncionamento === "string") {

                try {

                    diasFuncionamento =
                        JSON.parse(diasFuncionamento);

                } catch (erro) {

                    console.error(
                        "Erro ao interpretar dias_funcionamento:",
                        erro
                    );

                    diasFuncionamento = {};

                }

            }


            // Garante que seja um objeto
            if (
                !diasFuncionamento ||
                typeof diasFuncionamento !== "object" ||
                Array.isArray(diasFuncionamento)
            ) {

                diasFuncionamento = {};

            }
      configBanco.dias_funcionamento =
                diasFuncionamento;
                atualizarTextosSobre(config);
    })
    .catch(erro => console.error('Erro ao carregar configurações na página Sobre:', erro));
}

function atualizarTextosSobre(config) {
  // Formatação de horários para exibir apenas HH:MM
  const abertura = config.horario_abertura ? config.horario_abertura.substring(0, 5) : '09:00';
  const fechamento = config.horario_fechamento ? config.horario_fechamento.substring(0, 5) : '17:00';

  // --- FAQ 1: Mínimo de pessoas na reserva ---
  const faqReserva = document.querySelector('.faq-lista .faq-item:nth-child(1) .faq-resposta p');
  if (faqReserva) {
    faqReserva.innerHTML = `Para mesas individuais e pequenas famílias, o atendimento é por <strong>ordem de chegada</strong>. As reservas pelo site são exclusivas para <strong>grupos e excursões</strong> a partir de ${config.min_pessoas_reserva} pessoas.`;
  }

  // --- Ocultar ou exibir a seção/card do Beach Club de acordo com a configuração ---
  const secaoBeachClub = document.querySelector('.beach-club-destaque');
  const cardPilarBeach = document.querySelector('.pilares-grid .card-pilar:nth-child(2)');

  if (Number(config.permitir_reserva_beach_club) === 0) {
    if (secaoBeachClub) secaoBeachClub.style.display = 'none';
    if (cardPilarBeach) cardPilarBeach.style.display = 'none';
  } else {
    if (secaoBeachClub) secaoBeachClub.style.display = 'flex';
    if (cardPilarBeach) cardPilarBeach.style.display = 'block';
  }

  // --- FAQ 4: Detalhes do Beach Club ---
  const faqBeach = document.querySelector('.faq-lista .faq-item:nth-child(4) .faq-resposta p');
  if (faqBeach) {
    if (Number(config.permitir_reserva_beach_club) === 1) {
      const taxa = parseFloat(config.taxa_pessoa_beach_club || 0).toFixed(2).replace('.', ',');
      faqBeach.innerHTML = `A estrutura conta com lounges, acesso à praia, piscina e bar exclusivo de drinks. A taxa de utilização é de R$ ${taxa} por pessoa.`;
    } else {
      faqBeach.innerHTML = `No momento, a área de Beach Club não está disponível para reservas.`;
    }
  }

  // --- Cartão de Horários Práticos ---
  const elemHorariosPraticos = document.querySelector('.card-info-pratica:nth-child(1) ul');
  if (elemHorariosPraticos) {
    elemHorariosPraticos.innerHTML = `
      <li><strong>Todos os dias:</strong> ${abertura}h às ${fechamento}h</li>
      <li><strong>Buffet a Kilo:</strong> Diariamente das 12h às 15h</li>
    `;
  }

  // --- Horários no Rodapé ---
  const elemHorarioFooter =
        document.querySelector(
            ".funcionamento p:nth-child(2)"
        );


    if (elemHorarioFooter) {

        const nomesDias = [
            "Domingo",
            "Segunda-feira",
            "Terça-feira",
            "Quarta-feira",
            "Quinta-feira",
            "Sexta-feira",
            "Sábado"
        ];


        const dias =
            configBanco.dias_funcionamento || {};

        const textosDias = [];


        Object.keys(dias).forEach((dia) => {

            const horario =
                dias[dia];


            if (
                horario &&
                horario.abertura &&
                horario.fechamento
            ) {

                textosDias.push(
                    `${nomesDias[Number(dia)]}: ${horario.abertura.substring(0, 5)}h às ${horario.fechamento.substring(0, 5)}h`
                );

            }

        });


        if (textosDias.length > 0) {

            elemHorarioFooter.innerHTML =
                `<strong>Restaurante:</strong> ${textosDias.join(" | ")}.`;

        } 
  }
}